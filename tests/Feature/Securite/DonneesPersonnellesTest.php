<?php

namespace Tests\Feature\Securite;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonneesPersonnellesTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    public function test_personal_data_is_visible_to_admin_only(): void
    {
        $beneficiaire = $this->creerBeneficiaireAvecDemande();
        $demande = $beneficiaire->demandesOrientation()->first();
        $pages = ['/admin/beneficiaires', "/admin/beneficiaires/{$beneficiaire->id}", '/admin/demandes', "/admin/demandes/{$demande->id}"];

        foreach ($pages as $page) {
            // Visiteur : redirigé vers la connexion, aucune donnée affichée
            $this->app['auth']->forgetGuards();
            $this->get($page)->assertRedirect('/login')->assertDontSee('Awa Sanni');

            // Utilisateur ordinaire : refusé, aucune donnée affichée
            $this->actingAs(User::factory()->create())->get($page)
                ->assertForbidden()->assertDontSee('Awa Sanni')->assertDontSee('awa@example.com');
        }

        $this->actingAs($this->creerAdmin())->get("/admin/beneficiaires/{$beneficiaire->id}")
            ->assertOk()->assertSee('Awa Sanni');
    }

    public function test_public_pages_do_not_expose_beneficiaries(): void
    {
        $this->creerBeneficiaireAvecDemande();
        $formation = $this->creerFormation();

        foreach (['/', '/a-propos', '/formations', "/formations/{$formation->id}", '/orientation', '/seances'] as $page) {
            $this->get($page)->assertOk()
                ->assertDontSee('Awa Sanni')
                ->assertDontSee('awa@example.com')
                ->assertDontSee('0197000001');
        }
    }

    public function test_ordinary_user_cannot_write_follow_up_notes_or_answers(): void
    {
        $beneficiaire = $this->creerBeneficiaireAvecDemande();
        $demande = $beneficiaire->demandesOrientation()->first();
        $utilisateur = User::factory()->create();

        $this->actingAs($utilisateur)->post("/admin/beneficiaires/{$beneficiaire->id}/suivis", ['note' => 'x'])->assertForbidden();
        $this->actingAs($utilisateur)->post("/admin/demandes/{$demande->id}/repondre", ['reponse_admin' => 'x'])->assertForbidden();

        $this->assertDatabaseCount('suivis', 0);
        $this->assertDatabaseHas('demande_orientations', ['id' => $demande->id, 'statut' => 'nouvelle', 'reponse_admin' => null]);
    }

    public function test_sensitive_files_are_not_served_from_public_folder(): void
    {
        $this->assertFileDoesNotExist(public_path('.env'));
        $this->assertFileDoesNotExist(public_path('database.sqlite'));
        $this->assertDoesNotMatchRegularExpression('/[\w.+-]+@gmail\.com/', file_get_contents(config_path('mail.php')),
            'Aucune adresse e-mail personnelle codée en dur dans la configuration.');
    }
}
