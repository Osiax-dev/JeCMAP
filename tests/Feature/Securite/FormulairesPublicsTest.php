<?php

namespace Tests\Feature\Securite;

use App\Http\Middleware\ProtectionAntiRobot;
use App\Mail\NouvelleDemandeOrientation;
use App\Models\Beneficiaire;
use App\Models\Candidature;
use App\Models\DemandeOrientation;
use App\Models\InscriptionSeance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FormulairesPublicsTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['mail.jecmap_admin' => 'equipe@jecmap.test']);
    }

    private function demande(array $champs = []): array
    {
        return array_merge([
            'nom' => 'Koffi Adjovi',
            'telephone' => '+229 01 96 00 00 01',
            'statut_scolaire' => 'lyceen',
            'besoin' => 'Je veux savoir quelle filière choisir après le BAC.',
        ], $this->champsAntiRobot(), $champs);
    }

    // ---------- Fonctionnement légitime conservé ----------

    public function test_valid_orientation_request_is_saved_and_notified(): void
    {
        $this->from('/orientation')->post('/orientation', $this->demande())
            ->assertRedirect('/orientation#messages-formulaire')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('beneficiaires', ['nom' => 'Koffi Adjovi', 'telephone_normalise' => '0196000001']);
        $this->assertSame(1, DemandeOrientation::count());
        // Phase 2 : l'e-mail est mis en file d'attente au lieu d'être envoyé pendant la requête.
        Mail::assertQueued(NouvelleDemandeOrientation::class, fn ($m) => $m->hasTo('equipe@jecmap.test'));
    }

    public function test_valid_application_and_session_registration_are_saved(): void
    {
        $formation = $this->creerFormation();
        $seance = $this->creerSeance();

        $this->post("/formations/{$formation->id}/candidater", ['nom' => 'Awa', 'telephone' => '0197000002'] + $this->champsAntiRobot())
            ->assertSessionHas('success');
        $this->post("/seances/{$seance->id}/inscription", ['nom' => 'Awa', 'telephone' => '0197000002', 'statut_scolaire' => 'etudiant'] + $this->champsAntiRobot())
            ->assertSessionHas('success');

        $this->assertSame(1, Candidature::count());
        $this->assertSame(1, InscriptionSeance::count());
        $this->assertSame(1, Beneficiaire::count(), 'La même personne ne doit pas être créée deux fois.');
    }

    public function test_public_forms_contain_anti_robot_fields(): void
    {
        $formation = $this->creerFormation();
        $this->creerSeance();

        foreach (['/orientation', "/formations/{$formation->id}", '/seances'] as $page) {
            $this->get($page)->assertOk()
                ->assertSee('name="site_internet"', false)
                ->assertSee('name="_debut_formulaire"', false);
        }
    }

    // ---------- Anti-robot ----------

    public function test_honeypot_filled_is_rejected(): void
    {
        $this->post('/orientation', $this->demande(['site_internet' => 'http://spam.example']))
            ->assertSessionHasErrors(['formulaire' => ProtectionAntiRobot::MESSAGE]);

        $this->assertSame(0, Beneficiaire::count());
        Mail::assertNothingOutgoing();
    }

    public function test_submission_too_fast_is_rejected(): void
    {
        $this->post('/orientation', $this->demande($this->champsAntiRobot(1)))
            ->assertSessionHasErrors('formulaire');

        $this->assertSame(0, Beneficiaire::count());
    }

    public function test_missing_or_forged_token_is_rejected(): void
    {
        $sansJeton = $this->demande();
        unset($sansJeton['_debut_formulaire']);

        $this->post('/orientation', $sansJeton)->assertSessionHasErrors('formulaire');
        $this->post('/orientation', $this->demande(['_debut_formulaire' => (string) (now()->timestamp - 60)]))
            ->assertSessionHasErrors('formulaire');

        $this->assertSame(0, Beneficiaire::count());
    }

    public function test_anti_robot_applies_to_all_public_forms(): void
    {
        $formation = $this->creerFormation();
        $seance = $this->creerSeance();
        $robot = ['site_internet' => 'spam'] + $this->champsAntiRobot();

        $this->post("/formations/{$formation->id}/candidater", ['nom' => 'Bot', 'telephone' => '0190000000'] + $robot)
            ->assertSessionHasErrors('formulaire');
        $this->post("/seances/{$seance->id}/inscription", ['nom' => 'Bot', 'telephone' => '0190000000', 'statut_scolaire' => 'autre'] + $robot)
            ->assertSessionHasErrors('formulaire');

        $this->assertSame(0, Beneficiaire::count());
    }

    // ---------- Limite d'envoi (throttle) ----------

    public function test_rate_limit_blocks_repeated_submissions_from_same_ip(): void
    {
        $seance = $this->creerSeance();

        for ($i = 1; $i <= 10; $i++) {
            $this->post("/seances/{$seance->id}/inscription", [
                'nom' => "Jeune {$i}", 'telephone' => '01970000'.str_pad((string) $i, 2, '0', STR_PAD_LEFT), 'statut_scolaire' => 'lyceen',
            ] + $this->champsAntiRobot())->assertSessionHas('success');
        }

        $this->post("/seances/{$seance->id}/inscription", [
            'nom' => 'Jeune 11', 'telephone' => '0197000011', 'statut_scolaire' => 'lyceen',
        ] + $this->champsAntiRobot())->assertSessionHasErrors('formulaire');

        $this->assertSame(10, InscriptionSeance::count());
    }

    // ---------- Doublons ----------

    public function test_same_person_is_not_duplicated_across_submissions(): void
    {
        $seance = $this->creerSeance();

        // Même numéro écrit différemment, même nom (casse/accents différents)
        foreach (['+229 01 96 00 00 09', '0196000009', '00229 0196 000 009'] as $tel) {
            $this->post("/seances/{$seance->id}/inscription", [
                'nom' => 'Élodie  Houngbo', 'telephone' => $tel, 'statut_scolaire' => 'lyceen',
            ] + $this->champsAntiRobot());
        }
        $this->post("/seances/{$seance->id}/inscription", [
            'nom' => 'elodie houngbo', 'telephone' => '0196000009', 'statut_scolaire' => 'lyceen',
        ] + $this->champsAntiRobot());

        $this->assertSame(1, Beneficiaire::count());
        $this->assertSame(1, InscriptionSeance::count(), 'Une seule inscription par personne et par séance.');
    }

    public function test_siblings_sharing_a_phone_stay_distinct(): void
    {
        $seance = $this->creerSeance();

        foreach (['Rachidatou Bio', 'Ibrahim Bio'] as $nom) {
            $this->post("/seances/{$seance->id}/inscription", [
                'nom' => $nom, 'telephone' => '0196000010', 'statut_scolaire' => 'lyceen',
            ] + $this->champsAntiRobot());
        }

        $this->assertSame(2, Beneficiaire::count());
        $this->assertSame(2, InscriptionSeance::count());
    }

    public function test_duplicate_application_to_same_formation_is_not_recorded(): void
    {
        $formation = $this->creerFormation();
        $donnees = ['nom' => 'Awa', 'telephone' => '0197000002'];

        $this->post("/formations/{$formation->id}/candidater", $donnees + $this->champsAntiRobot());
        $this->post("/formations/{$formation->id}/candidater", $donnees + $this->champsAntiRobot())
            ->assertSessionHas('success');

        $this->assertSame(1, Candidature::count());
    }

    public function test_second_pending_orientation_request_is_refused(): void
    {
        $this->post('/orientation', $this->demande());
        $this->post('/orientation', $this->demande(['besoin' => 'Une autre question sur les bourses.']))
            ->assertSessionHasErrors('formulaire');

        $this->assertSame(1, DemandeOrientation::count());
        Mail::assertQueuedCount(1);
    }

    // ---------- Validation serveur ----------

    public function test_server_side_validation_rejects_invalid_data(): void
    {
        $this->post('/orientation', $this->demande([
            'telephone' => 'abc', 'statut_scolaire' => 'pirate', 'besoin' => 'court',
        ]))->assertSessionHasErrors(['telephone', 'statut_scolaire', 'besoin']);

        $this->post('/orientation', $this->demande(['telephone' => null, 'email' => null]))
            ->assertSessionHasErrors(['telephone', 'email']);

        $this->post('/orientation', $this->demande(['nom' => str_repeat('a', 300)]))
            ->assertSessionHasErrors('nom');

        $this->assertSame(0, Beneficiaire::count());
    }

    public function test_closed_formation_and_past_session_refuse_submissions(): void
    {
        $formation = $this->creerFormation(['statut' => 'cloturee']);
        $passee = $this->creerSeance(['statut' => 'passee']);
        $dateDepassee = $this->creerSeance(['date_seance' => now()->subDay()]);

        $this->post("/formations/{$formation->id}/candidater", ['nom' => 'Awa', 'telephone' => '0197000002'] + $this->champsAntiRobot())
            ->assertSessionHasErrors('formulaire');

        foreach ([$passee, $dateDepassee] as $seance) {
            $this->post("/seances/{$seance->id}/inscription", ['nom' => 'Awa', 'telephone' => '0197000002', 'statut_scolaire' => 'lyceen'] + $this->champsAntiRobot())
                ->assertSessionHasErrors('formulaire');
        }

        $this->assertSame(0, Candidature::count() + InscriptionSeance::count());
    }

    public function test_missing_admin_address_does_not_send_mail_to_a_hardcoded_person(): void
    {
        config(['mail.jecmap_admin' => null]);

        $this->post('/orientation', $this->demande())->assertSessionHas('success');

        $this->assertSame(1, DemandeOrientation::count());
        Mail::assertNothingOutgoing();
    }
}
