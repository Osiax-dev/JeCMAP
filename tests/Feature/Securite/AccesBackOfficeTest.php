<?php

namespace Tests\Feature\Securite;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

class AccesBackOfficeTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    /** Toutes les routes du back-office, avec des paramètres existants. */
    private function routesAdmin(): array
    {
        $formation = $this->creerFormation();
        $seance = $this->creerSeance();
        $beneficiaire = $this->creerBeneficiaireAvecDemande();
        $demande = $beneficiaire->demandesOrientation()->first();

        $parametres = [
            'formation' => $formation->id,
            'seance' => $seance->id,
            'beneficiaire' => $beneficiaire->id,
            'demande' => $demande->id,
        ];

        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $r) => str_starts_with($r->uri(), 'admin'))
            ->flatMap(function (Route $r) use ($parametres) {
                $uri = preg_replace_callback('/\{(\w+)\??\}/', fn ($m) => $parametres[$m[1]], $r->uri());

                return collect($r->methods())
                    ->reject(fn ($m) => $m === 'HEAD')
                    ->map(fn ($methode) => [$methode, '/'.$uri]);
            })
            ->values()
            ->all();

        $this->assertNotEmpty($routes, 'Aucune route admin trouvée.');

        return $routes;
    }

    public function test_every_admin_route_requires_admin_middleware(): void
    {
        $sansProtection = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $r) => str_starts_with($r->uri(), 'admin'))
            ->reject(fn (Route $r) => in_array('auth', $r->gatherMiddleware()) && in_array('admin', $r->gatherMiddleware()))
            ->map(fn (Route $r) => $r->uri())
            ->values()
            ->all();

        $this->assertSame([], $sansProtection, 'Routes admin sans middleware auth + admin.');
    }

    public function test_guest_is_redirected_to_login_on_every_admin_route(): void
    {
        foreach ($this->routesAdmin() as [$methode, $uri]) {
            $this->call($methode, $uri)->assertRedirect('/login');
        }
    }

    public function test_ordinary_user_gets_403_on_every_admin_route(): void
    {
        $utilisateur = User::factory()->create();

        foreach ($this->routesAdmin() as [$methode, $uri]) {
            $this->actingAs($utilisateur)->call($methode, $uri)->assertForbidden();
        }
    }

    public function test_ordinary_user_is_forbidden_from_dashboard_redirect(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertForbidden();
    }

    public function test_ordinary_user_cannot_modify_or_delete_data(): void
    {
        $utilisateur = User::factory()->create();
        $formation = $this->creerFormation(['titre' => 'Titre original']);
        $seance = $this->creerSeance();

        $this->actingAs($utilisateur)
            ->put("/admin/formations/{$formation->id}", ['titre' => 'Piraté', 'statut' => 'active'])
            ->assertForbidden();
        $this->actingAs($utilisateur)->delete("/admin/formations/{$formation->id}")->assertForbidden();
        $this->actingAs($utilisateur)->delete("/admin/seances/{$seance->id}")->assertForbidden();

        $this->assertDatabaseHas('formations', ['id' => $formation->id, 'titre' => 'Titre original']);
        $this->assertDatabaseHas('seance_orientations', ['id' => $seance->id]);
    }

    public function test_admin_keeps_access_to_back_office_pages(): void
    {
        $admin = $this->creerAdmin();
        $beneficiaire = $this->creerBeneficiaireAvecDemande();
        $demande = $beneficiaire->demandesOrientation()->first();
        $formation = $this->creerFormation();
        $seance = $this->creerSeance();

        foreach ([
            '/admin/dashboard',
            '/admin/demandes',
            "/admin/demandes/{$demande->id}",
            '/admin/formations',
            '/admin/formations/create',
            "/admin/formations/{$formation->id}/edit",
            '/admin/seances',
            '/admin/seances/create',
            "/admin/seances/{$seance->id}",
            "/admin/seances/{$seance->id}/edit",
            '/admin/beneficiaires',
            "/admin/beneficiaires/{$beneficiaire->id}",
        ] as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }

        $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_still_manage_content(): void
    {
        $admin = $this->creerAdmin();
        $beneficiaire = $this->creerBeneficiaireAvecDemande();
        $demande = $beneficiaire->demandesOrientation()->first();

        $this->actingAs($admin)->post('/admin/formations', ['titre' => 'Bureautique', 'duree_jours' => 5])
            ->assertRedirect(route('admin.formations.index'));
        $this->assertDatabaseHas('formations', ['titre' => 'Bureautique', 'created_by' => $admin->id]);

        $this->actingAs($admin)->post('/admin/seances', [
            'titre' => 'Préparer son CV', 'date_seance' => now()->addDays(3)->toDateTimeString(), 'format' => 'en_ligne',
        ])->assertRedirect(route('admin.seances.index'));
        $this->assertDatabaseHas('seance_orientations', ['titre' => 'Préparer son CV']);

        $this->actingAs($admin)->post("/admin/demandes/{$demande->id}/repondre", ['reponse_admin' => 'Rendez-vous lundi.'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('demande_orientations', ['id' => $demande->id, 'statut' => 'traitee']);

        $this->actingAs($admin)->post("/admin/beneficiaires/{$beneficiaire->id}/suivis", ['note' => 'Appelé ce jour.'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('suivis', ['beneficiaire_id' => $beneficiaire->id, 'user_id' => $admin->id]);
    }

    public function test_is_admin_cannot_be_mass_assigned(): void
    {
        $user = User::create(['name' => 'X', 'email' => 'x@example.com', 'password' => 'password', 'is_admin' => true]);

        $this->assertFalse($user->fresh()->isAdmin());
    }

    public function test_admin_command_promotes_and_demotes_an_account(): void
    {
        $user = User::factory()->create(['email' => 'equipe@jecmap.org']);

        $this->artisan('jecmap:admin', ['email' => 'equipe@jecmap.org'])->assertSuccessful();
        $this->assertTrue($user->fresh()->isAdmin());

        $this->artisan('jecmap:admin', ['email' => 'equipe@jecmap.org', '--retirer' => true])->assertSuccessful();
        $this->assertFalse($user->fresh()->isAdmin());
    }

    public function test_admin_command_creates_a_new_admin(): void
    {
        $this->artisan('jecmap:admin', ['email' => 'nouvel.admin@jecmap.org'])
            ->expectsQuestion('Nom affiché', 'Nouvel Admin')
            ->expectsQuestion('Mot de passe (12 caractères minimum)', 'MotDePasse-Solide-2026')
            ->expectsQuestion('Confirme le mot de passe', 'MotDePasse-Solide-2026')
            ->assertSuccessful();

        $admin = User::where('email', 'nouvel.admin@jecmap.org')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertNotSame('MotDePasse-Solide-2026', $admin->password, 'Le mot de passe doit être haché.');
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
