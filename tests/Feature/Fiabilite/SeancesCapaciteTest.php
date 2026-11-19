<?php

namespace Tests\Feature\Fiabilite;

use App\Models\Beneficiaire;
use App\Models\InscriptionSeance;
use App\Models\SeanceOrientation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Securite\FabriqueDonnees;
use Tests\TestCase;

class SeancesCapaciteTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    private function inscrire(SeanceOrientation $seance, string $nom, string $telephone): TestResponse
    {
        return $this->from('/seances')->post("/seances/{$seance->id}/inscription", [
            'nom' => $nom, 'telephone' => $telephone, 'statut_scolaire' => 'lyceen', 'seance_formulaire' => $seance->id,
        ] + $this->champsAntiRobot());
    }

    public function test_registrations_stop_exactly_at_capacity(): void
    {
        $seance = $this->creerSeance(['places' => 2]);

        $this->inscrire($seance, 'Jeune Un', '0197000001')->assertSessionHas('success');
        $this->inscrire($seance, 'Jeune Deux', '0197000002')->assertSessionHas('success');
        $this->inscrire($seance, 'Jeune Trois', '0197000003')->assertSessionHasErrors('formulaire');

        $this->assertSame(2, $seance->inscriptions()->count());
        $this->assertNull(Beneficiaire::where('nom', 'Jeune Trois')->first(), 'Aucun bénéficiaire créé pour une séance complète.');
    }

    public function test_already_registered_person_is_told_so_even_when_full(): void
    {
        $seance = $this->creerSeance(['places' => 1]);

        $this->inscrire($seance, 'Jeune Un', '0197000001')->assertSessionHas('success');
        $this->inscrire($seance, 'Jeune Un', '+229 01 97 00 00 01')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Tu es déjà inscrit à cette séance. On t\'attend !');

        $this->assertSame(1, InscriptionSeance::count());
    }

    public function test_unlimited_places_when_capacity_is_empty(): void
    {
        $seance = $this->creerSeance(['places' => null]);

        for ($i = 1; $i <= 5; $i++) {
            $this->inscrire($seance, "Jeune {$i}", '019700000'.$i)->assertSessionHas('success');
        }

        $this->assertSame(5, $seance->inscriptions()->count());
        $this->assertNull($seance->fresh()->placesRestantes());
    }

    public function test_capacity_helpers(): void
    {
        $seance = $this->creerSeance(['places' => 3]);
        $this->assertSame(3, $seance->placesRestantes());
        $this->assertFalse($seance->estComplete());

        foreach (['0197000001', '0197000002', '0197000003'] as $i => $tel) {
            $this->inscrire($seance, "Jeune {$i}", $tel);
        }

        $seance = $seance->fresh();
        $this->assertSame(0, $seance->placesRestantes());
        $this->assertTrue($seance->estComplete());
    }

    public function test_public_page_shows_remaining_places_and_hides_form_when_full(): void
    {
        $presque = $this->creerSeance(['titre' => 'Séance presque pleine', 'places' => 2]);
        $pleine = $this->creerSeance(['titre' => 'Séance pleine', 'places' => 1]);
        $this->inscrire($presque, 'Jeune Un', '0197000001');
        $this->inscrire($pleine, 'Jeune Deux', '0197000002');

        $page = $this->get('/seances')->assertOk();
        $page->assertSee('1 place restante');
        $page->assertSee('Séance complète : inscriptions closes.');
        $page->assertSee(route('seances.inscrire', $presque), false);
        $page->assertDontSee(route('seances.inscrire', $pleine), false);
    }

    public function test_past_and_closed_sessions_are_hidden_and_refused(): void
    {
        $passee = $this->creerSeance(['titre' => 'Séance statut passée', 'statut' => 'passee']);
        $dateDepassee = $this->creerSeance(['titre' => 'Séance date dépassée', 'date_seance' => now()->subHour()]);
        $aVenir = $this->creerSeance(['titre' => 'Séance à venir']);

        $this->get('/seances')->assertOk()
            ->assertSee('Séance à venir')
            ->assertDontSee('Séance statut passée')
            ->assertDontSee('Séance date dépassée');

        $this->inscrire($passee, 'Awa', '0197000001')->assertSessionHasErrors('formulaire');
        $this->inscrire($dateDepassee, 'Awa', '0197000001')->assertSessionHasErrors('formulaire');
        $this->inscrire($aVenir, 'Awa', '0197000001')->assertSessionHas('success');

        $this->assertSame(1, InscriptionSeance::count());
    }

    public function test_registration_uses_a_row_lock(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/SeanceOrientationController.php'));

        $this->assertStringContainsString('lockForUpdate()', $source);
        $this->assertStringContainsString('DB::transaction', $source);
    }

    public function test_admin_cannot_set_capacity_below_registrations(): void
    {
        $admin = $this->creerAdmin();
        $seance = $this->creerSeance(['places' => 5]);
        $this->inscrire($seance, 'Jeune Un', '0197000001');
        $this->inscrire($seance, 'Jeune Deux', '0197000002');

        $donnees = ['titre' => $seance->titre, 'date_seance' => $seance->date_seance->toDateTimeString(), 'format' => 'presentiel', 'statut' => 'a_venir'];

        $this->actingAs($admin)->from("/admin/seances/{$seance->id}/edit")
            ->put("/admin/seances/{$seance->id}", $donnees + ['places' => 1])
            ->assertSessionHasErrors('places');
        $this->assertSame(5, $seance->fresh()->places);

        $this->actingAs($admin)->put("/admin/seances/{$seance->id}", $donnees + ['places' => 2])->assertSessionHasNoErrors();
        $this->assertSame(2, $seance->fresh()->places);

        $this->actingAs($admin)->put("/admin/seances/{$seance->id}", $donnees + ['places' => null])->assertSessionHasNoErrors();
        $this->assertNull($seance->fresh()->places);
    }

    public function test_admin_can_close_a_session(): void
    {
        $seance = $this->creerSeance();

        $this->actingAs($this->creerAdmin())->put("/admin/seances/{$seance->id}", [
            'titre' => $seance->titre, 'date_seance' => $seance->date_seance->toDateTimeString(), 'format' => 'presentiel', 'statut' => 'passee',
        ])->assertSessionHasNoErrors();

        $this->inscrire($seance, 'Awa', '0197000001')->assertSessionHasErrors('formulaire');
    }

    public function test_session_with_registrations_cannot_be_deleted(): void
    {
        $seance = $this->creerSeance();
        $this->inscrire($seance, 'Awa', '0197000001');

        $this->actingAs($this->creerAdmin())->from('/admin/seances')
            ->delete("/admin/seances/{$seance->id}")->assertSessionHasErrors('formulaire');

        $this->assertDatabaseHas('seance_orientations', ['id' => $seance->id]);
        $this->assertSame(1, InscriptionSeance::count());

        $vide = $this->creerSeance();
        $this->actingAs($this->creerAdmin())->delete("/admin/seances/{$vide->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('seance_orientations', ['id' => $vide->id]);
    }

    public function test_admin_capacity_validation_rejects_zero_or_negative(): void
    {
        $this->actingAs($this->creerAdmin())->post('/admin/seances', [
            'titre' => 'Test', 'date_seance' => now()->addDay()->toDateTimeString(), 'format' => 'presentiel', 'places' => 0,
        ])->assertSessionHasErrors('places');

        $this->assertSame(0, SeanceOrientation::count());
    }
}
