<?php

namespace Tests\Feature\Interface;

use App\Models\Beneficiaire;
use App\Models\InscriptionSeance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Securite\FabriqueDonnees;
use Tests\TestCase;

/** Phase 3 : nouvelle identité (direction D), layout public, accueil et système de mouvement. */
class AccueilEtLayoutTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    private const PAGES_PUBLIQUES = [
        '/' => 'Accueil',
        '/a-propos' => 'À propos',
        '/formations' => 'Formations',
        '/orientation' => 'Orientation',
        '/seances' => 'Séances',
    ];

    // ---------- Accueil : prochaine séance (données réelles) ----------

    public function test_home_shows_the_next_open_session_with_remaining_places(): void
    {
        $this->creerSeance(['titre' => 'Séance plus tardive', 'date_seance' => now()->addWeeks(3)]);
        $this->creerSeance(['titre' => 'Préparer son CV', 'date_seance' => now()->addDays(5)->setTime(10, 0), 'lieu' => 'Natitingou', 'places' => 30]);

        $this->get('/')->assertOk()
            ->assertSee('Prochaine séance collective')
            ->assertSee('Préparer son CV')
            ->assertDontSee('Séance plus tardive')
            ->assertSee('Natitingou')
            ->assertSee('30 places restantes')
            ->assertSee('à 10h00')
            ->assertSee('Je m’inscris');
    }

    public function test_home_ignores_past_and_closed_sessions(): void
    {
        $this->creerSeance(['titre' => 'Séance passée', 'statut' => 'passee']);
        $this->creerSeance(['titre' => 'Séance déjà dépassée', 'date_seance' => now()->subDay()]);

        $this->get('/')->assertOk()
            ->assertDontSee('Séance passée')
            ->assertDontSee('Séance déjà dépassée')
            ->assertSee('Aucune séance programmée pour le moment')
            ->assertSee(route('orientation.create'), false);
    }

    public function test_home_shows_full_session_without_registration_button(): void
    {
        $seance = $this->creerSeance(['titre' => 'Séance pleine', 'places' => 1]);
        $b = Beneficiaire::create(['nom' => 'Awa', 'telephone' => '0197000001', 'telephone_normalise' => '0197000001']);
        InscriptionSeance::create(['seance_id' => $seance->id, 'beneficiaire_id' => $b->id, 'statut' => 'inscrit']);

        $this->get('/')->assertOk()
            ->assertSee('Séance pleine')
            ->assertSee('Complet')
            ->assertSee('Voir les autres séances')
            ->assertDontSee('Je m’inscris');
    }

    public function test_home_session_with_unlimited_places_shows_no_counter(): void
    {
        $this->creerSeance(['titre' => 'Séance ouverte à tous', 'places' => null]);

        $this->get('/')->assertOk()->assertSee('Séance ouverte à tous')->assertDontSee('places restantes')->assertDontSee('Complet');
    }

    public function test_home_content_structure(): void
    {
        $page = $this->get('/')->assertOk();

        $page->assertSee('Choisir sa voie.')
            ->assertSee('Construire son avenir.')
            ->assertSee('id="parcours"', false)
            ->assertSee('id="mission"', false)
            ->assertSee('Étape 4 : ', false)
            ->assertSee('data-words', false)
            ->assertSee('fetchpriority="high"', false);

        $this->assertSame(1, substr_count($page->getContent(), '<h1'), 'Un seul titre h1 par page.');
    }

    // ---------- Layout public ----------

    public function test_every_public_page_uses_the_new_layout(): void
    {
        $this->creerFormation();

        foreach (array_keys(self::PAGES_PUBLIQUES) as $url) {
            $this->get($url)->assertOk()
                ->assertSee('class="skip-link" href="#contenu"', false)
                ->assertSee('<main id="contenu"', false)
                ->assertSee('<meta name="description"', false)
                ->assertSee('css/jecmap-v2.css', false)
                ->assertSee('js/jecmap.js', false)
                ->assertSee('id="contact"', false);
        }
    }

    public function test_current_page_is_marked_in_navigation(): void
    {
        foreach (self::PAGES_PUBLIQUES as $url => $libelle) {
            $html = $this->get($url)->getContent();

            $this->assertMatchesRegularExpression(
                '/class="d-nav__link"\s+aria-current="page"\s*>'.preg_quote($libelle, '/').'</u',
                $html,
                "Le lien « {$libelle} » doit porter aria-current sur {$url}."
            );
            $this->assertSame(2, substr_count($html, 'aria-current="page"'), "Un lien actif dans le menu bureau et un dans le menu mobile ({$url}).");
        }
    }

    public function test_mobile_menu_is_accessible(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('aria-expanded="false" aria-controls="menu-mobile" data-menu-toggle', false)
            ->assertSee('<span class="sr-only">Ouvrir le menu</span>', false)
            ->assertSee('id="menu-mobile" hidden inert', false)
            ->assertSee('aria-label="Navigation mobile"', false);
    }

    public function test_old_scripts_and_broken_links_are_gone(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('typewriter', $html);
        $this->assertStringNotContainsString('new IntersectionObserver', $html, 'Les scripts sont regroupés dans public/js/jecmap.js.');
        $this->assertStringNotContainsString('Faire un don', $html, 'L\'ancien lien « Faire un don » menait aux séances.');
        $this->assertStringNotContainsString('favicon.png', $html, 'Le favicon pointait vers un fichier absent.');
        $this->assertStringNotContainsString('📍', $html, 'Pas d\'emoji comme icône.');
    }

    public function test_external_links_open_safely(): void
    {
        $html = $this->get('/')->getContent();

        preg_match_all('/<a [^>]*target="_blank"[^>]*>/', $html, $liens);
        $this->assertNotEmpty($liens[0]);
        foreach ($liens[0] as $lien) {
            $this->assertStringContainsString('rel="noopener"', $lien);
        }
    }

    // ---------- Fichiers de style et de mouvement ----------

    public function test_assets_exist_and_respect_motion_rules(): void
    {
        $css = file_get_contents(public_path('css/jecmap-v2.css'));
        $js = file_get_contents(public_path('js/jecmap.js'));

        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: no-preference)', $css);
        $this->assertStringNotContainsString('infinite', $css, 'Aucune animation infinie.');
        $this->assertStringContainsString('.js [data-reveal]', $css, 'États cachés seulement si JavaScript tourne.');
        $this->assertStringContainsString('@view-transition', $css);

        $this->assertStringContainsString("matchMedia('(prefers-reduced-motion: reduce)')", $js);
        $this->assertStringContainsString("e.key === 'Escape'", $js);
        $this->assertStringContainsString('IntersectionObserver', $js);
    }
}
