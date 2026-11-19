<?php

namespace Tests\Feature\Fiabilite;

use App\Mail\NouvelleDemandeOrientation;
use App\Mail\ReponseOrientation;
use App\Models\DemandeOrientation;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Tests\Feature\Securite\FabriqueDonnees;
use Tests\TestCase;

class FileAttenteEmailsTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'queue.default' => 'database',
            'mail.jecmap_admin' => 'equipe@jecmap.test',
            // Serveur SMTP injoignable : simule une panne Gmail.
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1,
            'mail.mailers.smtp.timeout' => 2,
        ]);
    }

    private function envoyerDemande(): TestResponse
    {
        return $this->from('/orientation')->post('/orientation', [
            'nom' => 'Koffi Adjovi',
            'telephone' => '0196000001',
            'statut_scolaire' => 'lyceen',
            'besoin' => 'Je veux savoir quelle filière choisir après le BAC.',
        ] + $this->champsAntiRobot());
    }

    public function test_mailables_are_queued_with_retries(): void
    {
        foreach ([NouvelleDemandeOrientation::class, ReponseOrientation::class] as $classe) {
            $mail = new $classe(new DemandeOrientation);

            $this->assertInstanceOf(ShouldQueue::class, $mail);
            $this->assertSame(4, $mail->tries);
            $this->assertSame([60, 300, 900], $mail->backoff());
            $this->assertTrue($mail->afterCommit);
        }
    }

    public function test_request_succeeds_and_is_saved_even_if_smtp_is_down(): void
    {
        $this->envoyerDemande()->assertSessionHas('success')->assertSessionHasNoErrors();

        $this->assertSame(1, DemandeOrientation::count());
        $this->assertDatabaseCount('jobs', 1);
    }

    public function test_failed_send_is_retried_then_stored_in_failed_jobs(): void
    {
        Log::spy();
        $this->envoyerDemande();

        // 1re tentative : échec SMTP, le job est remis en file pour plus tard (backoff), rien n'est perdu.
        $this->artisan('queue:work', ['--once' => true, '--stop-when-empty' => true]);
        $job = DB::table('jobs')->first();
        $this->assertNotNull($job, 'Le job doit être remis en file après un échec.');
        $this->assertSame(1, (int) $job->attempts);
        $this->assertGreaterThan(now()->timestamp, (int) $job->available_at, 'Nouvelle tentative différée.');
        $this->assertDatabaseCount('failed_jobs', 0);

        // Dernière tentative (simulée) : le job part dans failed_jobs et un avertissement est journalisé.
        DB::table('jobs')->update(['attempts' => 3, 'available_at' => now()->timestamp]);
        $this->artisan('queue:work', ['--once' => true, '--stop-when-empty' => true]);

        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('failed_jobs', 1);
        $this->assertSame(1, DemandeOrientation::count(), 'La demande reste enregistrée.');
        Log::shouldHaveReceived('warning')->withArgs(
            fn ($message, $contexte) => str_contains($message, 'Échec définitif') && isset($contexte['demande_id'])
        )->once();
    }

    public function test_failure_log_contains_no_personal_data(): void
    {
        Log::spy();
        $demande = $this->creerBeneficiaireAvecDemande()->demandesOrientation()->first();

        (new NouvelleDemandeOrientation($demande))->failed(new RuntimeException('Connexion SMTP refusée'));

        Log::shouldHaveReceived('warning')->withArgs(function ($message, $contexte) use ($demande) {
            $texte = $message.json_encode($contexte);

            return $contexte['demande_id'] === $demande->id
                && ! str_contains($texte, 'Awa') && ! str_contains($texte, 'awa@example.com') && ! str_contains($texte, '0197000001');
        })->once();
    }

    public function test_admin_reply_is_queued_and_saved(): void
    {
        Mail::fake();
        $admin = $this->creerAdmin();
        $demande = $this->creerBeneficiaireAvecDemande()->demandesOrientation()->first();

        $this->actingAs($admin)->post("/admin/demandes/{$demande->id}/repondre", ['reponse_admin' => 'Rendez-vous lundi à 10 h.'])
            ->assertSessionHas('success');

        Mail::assertQueued(ReponseOrientation::class, fn ($m) => $m->hasTo('awa@example.com'));
        Mail::assertNothingSent();
        $this->assertDatabaseHas('demande_orientations', ['id' => $demande->id, 'statut' => 'traitee']);
    }

    public function test_admin_reply_without_email_queues_nothing(): void
    {
        Mail::fake();
        $demande = $this->creerBeneficiaireAvecDemande()->demandesOrientation()->first();
        $demande->beneficiaire->update(['email' => null]);

        $this->actingAs($this->creerAdmin())->post("/admin/demandes/{$demande->id}/repondre", ['reponse_admin' => 'Appel prévu.'])
            ->assertSessionHas('success');

        Mail::assertNothingOutgoing();
    }

    public function test_queue_worker_is_scheduled_every_minute(): void
    {
        $evenement = collect(app(Schedule::class)->events())
            ->first(fn ($e) => str_contains($e->command ?? '', 'queue:work'));

        $this->assertNotNull($evenement, 'La commande queue:work doit être planifiée.');
        $this->assertSame('* * * * *', $evenement->expression);
        $this->assertStringContainsString('--stop-when-empty', $evenement->command);
    }
}
