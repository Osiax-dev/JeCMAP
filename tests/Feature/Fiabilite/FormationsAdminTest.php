<?php

namespace Tests\Feature\Fiabilite;

use App\Models\Beneficiaire;
use App\Models\Candidature;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Securite\FabriqueDonnees;
use Tests\TestCase;

class FormationsAdminTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    private function ajouterCandidature(Formation $formation, string $nom = 'Awa Sanni'): Candidature
    {
        $beneficiaire = Beneficiaire::create(['nom' => $nom, 'telephone' => '0197000001', 'telephone_normalise' => '0197000001']);

        return Candidature::create(['beneficiaire_id' => $beneficiaire->id, 'formation_id' => $formation->id, 'motivation' => 'Motivé', 'statut' => 'en_attente']);
    }

    public function test_show_page_no_longer_returns_500_and_lists_applications(): void
    {
        $formation = $this->creerFormation(['titre' => 'Bureautique']);
        $this->ajouterCandidature($formation, 'Awa Sanni');

        $this->actingAs($this->creerAdmin())->get("/admin/formations/{$formation->id}")
            ->assertOk()
            ->assertSee('Bureautique')
            ->assertSee('Candidatures (1)')
            ->assertSee('Awa Sanni');
    }

    public function test_show_page_with_no_application(): void
    {
        $formation = $this->creerFormation();

        $this->actingAs($this->creerAdmin())->get("/admin/formations/{$formation->id}")
            ->assertOk()->assertSee('Aucune candidature');
    }

    public function test_show_unknown_formation_returns_404(): void
    {
        $this->actingAs($this->creerAdmin())->get('/admin/formations/9999')->assertNotFound();
    }

    public function test_show_is_still_forbidden_to_non_admins(): void
    {
        $formation = $this->creerFormation();

        $this->actingAs(User::factory()->create())->get("/admin/formations/{$formation->id}")->assertForbidden();
        $this->app['auth']->forgetGuards();
        $this->get("/admin/formations/{$formation->id}")->assertRedirect('/login');
    }

    public function test_full_crud_cycle_with_photo(): void
    {
        Storage::fake('public');
        $admin = $this->creerAdmin();

        $this->actingAs($admin)->get('/admin/formations/create')->assertOk();
        $this->actingAs($admin)->post('/admin/formations', [
            'titre' => 'Couture', 'description' => 'Bases', 'duree_jours' => 10, 'places' => 15,
            'photo' => UploadedFile::fake()->image('couture.jpg'),
        ])->assertRedirect(route('admin.formations.index'));

        $formation = Formation::where('titre', 'Couture')->firstOrFail();
        Storage::disk('public')->assertExists($formation->photo);
        $this->assertSame('active', $formation->statut);

        $this->actingAs($admin)->get("/admin/formations/{$formation->id}/edit")->assertOk();
        $this->actingAs($admin)->put("/admin/formations/{$formation->id}", [
            'titre' => 'Couture avancée', 'duree_jours' => 12, 'places' => 10, 'statut' => 'cloturee',
        ])->assertRedirect(route('admin.formations.index'));
        $this->assertDatabaseHas('formations', ['id' => $formation->id, 'titre' => 'Couture avancée', 'statut' => 'cloturee']);

        $this->actingAs($admin)->get('/admin/formations')->assertOk()->assertSee('Couture avancée');

        $this->actingAs($admin)->delete("/admin/formations/{$formation->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('formations', ['id' => $formation->id]);
    }

    public function test_invalid_values_are_rejected_with_visible_errors(): void
    {
        $admin = $this->creerAdmin();

        $this->actingAs($admin)->from('/admin/formations/create')->post('/admin/formations', [
            'titre' => '', 'duree_jours' => -3, 'places' => 0,
        ])->assertSessionHasErrors(['titre', 'duree_jours', 'places'])->assertRedirect('/admin/formations/create');

        $this->actingAs($admin)->from('/admin/formations/create')->followingRedirects()
            ->post('/admin/formations', ['titre' => ''])
            ->assertSee('role="alert"', false);

        $this->assertSame(0, Formation::count());
    }

    public function test_formation_with_applications_cannot_be_deleted(): void
    {
        $formation = $this->creerFormation();
        $this->ajouterCandidature($formation);

        $this->actingAs($this->creerAdmin())->from('/admin/formations')
            ->delete("/admin/formations/{$formation->id}")
            ->assertSessionHasErrors('formulaire');

        $this->assertDatabaseHas('formations', ['id' => $formation->id]);
        $this->assertSame(1, Candidature::count());
    }
}
