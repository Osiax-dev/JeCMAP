<?php

namespace Tests\Feature\Interface;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Securite\FabriqueDonnees;
use Tests\TestCase;

/** Phase 3 : pages intérieures refaites (À propos, Formations, Orientation, Séances). */
class PagesInterieuresTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    public function test_each_page_has_its_own_title_description_and_single_h1(): void
    {
        $formation = $this->creerFormation(['titre' => 'Couture et stylisme', 'description' => 'Apprendre les bases de la couture.']);

        $pages = [
            '/a-propos' => 'À propos',
            '/formations' => 'Formations',
            "/formations/{$formation->id}" => 'Couture et stylisme',
            '/orientation' => 'Demande d&#039;orientation',
            '/seances' => 'Séances d&#039;orientation',
        ];

        $descriptions = [];
        foreach ($pages as $url => $titre) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString("<title>{$titre} — JeCMAP", $html, $url);
            $this->assertSame(1, substr_count($html, '<h1'), "Un seul h1 sur {$url}");
            preg_match('/<meta name="description" content="([^"]+)"/', $html, $m);
            $descriptions[$url] = $m[1] ?? '';
        }

        $this->assertSame(count($descriptions), count(array_unique($descriptions)), 'Chaque page a sa propre description.');
    }

    public function test_orientation_form_fields_have_linked_labels(): void
    {
        $html = $this->get('/orientation')->assertOk()->getContent();

        foreach (['o-nom', 'o-tel', 'o-email', 'o-niveau', 'o-besoin'] as $id) {
            $this->assertStringContainsString('for="'.$id.'"', $html, "Label relié à {$id}");
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString('<legend class="d-label">Tu es…</legend>', $html);
        $this->assertSame(5, substr_count($html, 'name="statut_scolaire"'), 'Cinq boutons radio pour le statut.');
        $this->assertStringContainsString('data-envoi', $html);
        $this->assertStringNotContainsString('<select', $html);
    }

    public function test_field_errors_are_announced_next_to_the_field(): void
    {
        $this->from('/orientation')->post('/orientation', [
            'nom' => '', 'telephone' => 'abc', 'statut_scolaire' => 'lyceen', 'besoin' => 'Une situation assez longue pour passer.',
        ] + $this->champsAntiRobot());

        $html = $this->get('/orientation')->getContent();

        $this->assertMatchesRegularExpression('/id="o-nom"[^>]*aria-invalid="true"[^>]*aria-describedby="o-nom-erreur"/', $html);
        $this->assertStringContainsString('id="o-nom-erreur"', $html);
        $this->assertStringContainsString('id="o-tel-erreur"', $html);
        $this->assertStringContainsString('2 points à corriger', $html);
    }

    public function test_formations_list_and_empty_state(): void
    {
        $this->get('/formations')->assertOk()
            ->assertSee('Aucune formation ouverte pour le moment')
            ->assertDontSee('formations ouvertes</li>', false);

        $formation = $this->creerFormation(['titre' => 'Bureautique', 'duree_jours' => 5, 'places' => 20]);
        $this->creerFormation(['titre' => 'Formation fermée', 'statut' => 'cloturee']);

        $this->get('/formations')->assertOk()
            ->assertSee('Bureautique')
            ->assertSee('5 jours')
            ->assertSee('20 places')
            ->assertSee('1 formation ouverte')
            ->assertSee(route('formations.show', $formation), false)
            ->assertDontSee('Formation fermée');
    }

    public function test_formation_detail_shows_real_content_only(): void
    {
        $formation = $this->creerFormation(['titre' => 'Couture', 'description' => 'Bases du patronage.', 'prerequis' => 'Savoir lire']);

        $this->get("/formations/{$formation->id}")->assertOk()
            ->assertSee('Bases du patronage.')
            ->assertSee('Prérequis')
            ->assertSee('Savoir lire')
            ->assertSee('Candidatures ouvertes')
            ->assertSee('for="f-nom"', false)
            ->assertDontSee('Ce que vous allez apprendre') // contenu générique inventé, retiré
            ->assertDontSee('⏱');
    }

    public function test_closed_formation_hides_the_application_form(): void
    {
        $formation = $this->creerFormation(['statut' => 'cloturee']);

        $this->get("/formations/{$formation->id}")->assertOk()
            ->assertSee('Les candidatures sont closes')
            ->assertSee('Candidatures closes')
            ->assertDontSee(route('formations.candidater', $formation), false);
    }

    public function test_sessions_page_shows_readable_details_and_empty_state(): void
    {
        $this->get('/seances')->assertOk()->assertSee('Aucune séance programmée pour le moment');

        $this->creerSeance([
            'titre' => 'Choisir sa filière après le BAC', 'lieu' => 'Natitingou', 'format' => 'en_ligne',
            'date_seance' => now()->addDays(3)->setTime(15, 30), 'places' => 25,
        ]);

        $this->get('/seances')->assertOk()
            ->assertSee('Choisir sa filière après le BAC')
            ->assertSee('En ligne')
            ->assertSee('Natitingou')
            ->assertSee('à 15h30')
            ->assertSee('25 places restantes')
            ->assertSee('S’inscrire à cette séance')
            ->assertSee('1 séance à venir')
            ->assertDontSee('📍');
    }

    public function test_about_page_keeps_its_content(): void
    {
        $this->get('/a-propos')->assertOk()
            ->assertSee('Une ONG de terrain,')
            ->assertSee('née à Natitingou.')
            ->assertSee('Accompagner chaque jeune vers son avenir.')
            ->assertSee('Gratuité')
            ->assertSee('Proximité')
            ->assertSee('Engagement')
            ->assertSee(route('orientation.create'), false);
    }
}
