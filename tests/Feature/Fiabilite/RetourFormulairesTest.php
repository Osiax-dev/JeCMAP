<?php

namespace Tests\Feature\Fiabilite;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Securite\FabriqueDonnees;
use Tests\TestCase;

/** Après un envoi, le visiteur revient directement sur le message (ancre) et retrouve ses saisies en cas d'erreur. */
class RetourFormulairesTest extends TestCase
{
    use FabriqueDonnees;
    use RefreshDatabase;

    private const ANCRE = '#messages-formulaire';

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function demande(array $champs = []): array
    {
        return array_merge([
            'nom' => 'Koffi Adjovi', 'telephone' => '0196000001', 'email' => 'koffi@example.com',
            'statut_scolaire' => 'etudiant', 'niveau' => 'Licence 2',
            'besoin' => 'Je veux savoir quelle filière choisir après le BAC.',
        ], $this->champsAntiRobot(), $champs);
    }

    public function test_success_redirects_to_message_anchor(): void
    {
        $this->from('/orientation')->post('/orientation', $this->demande())
            ->assertRedirect('/orientation'.self::ANCRE);
    }

    public function test_validation_error_redirects_to_anchor_and_keeps_input(): void
    {
        $this->from('/orientation')->post('/orientation', $this->demande(['besoin' => 'court']))
            ->assertRedirect('/orientation'.self::ANCRE)
            ->assertSessionHasErrors('besoin')
            ->assertSessionHasInput('nom', 'Koffi Adjovi')
            ->assertSessionHasInput('niveau', 'Licence 2');

        // La page réaffichée contient les saisies, le statut choisi et le message d'erreur.
        $html = $this->get('/orientation')
            ->assertSee('value="Koffi Adjovi"', false)
            ->assertSee('value="koffi@example.com"', false)
            ->assertSee('id="messages-formulaire"', false)
            ->assertSee('role="alert"', false)
            ->getContent();

        // Phase 3 : le statut est en boutons radio ; celui choisi reste coché.
        $this->assertMatchesRegularExpression('/name="statut_scolaire" value="etudiant"\s+checked/', $html);
        $this->assertDoesNotMatchRegularExpression('/value="lyceen"\s+checked/', $html);
        // Le champ en erreur est signalé et relié à son message.
        $this->assertStringContainsString('aria-describedby="o-besoin-aide o-besoin-erreur"', $html);
    }

    public function test_validation_messages_are_in_french_when_locale_is_fr(): void
    {
        app()->setLocale('fr');

        $this->from('/orientation')->post('/orientation', $this->demande(['besoin' => 'court', 'statut_scolaire' => '']))
            ->assertSessionHasErrors([
                'besoin' => 'Le champ description de ta situation doit contenir au moins 10 caractères.',
                'statut_scolaire' => 'Le champ statut est obligatoire.',
            ]);
    }

    public function test_anti_robot_and_rate_limit_also_redirect_to_anchor(): void
    {
        $this->from('/orientation')->post('/orientation', $this->demande(['site_internet' => 'spam']))
            ->assertRedirect('/orientation'.self::ANCRE)
            ->assertSessionHasInput('nom', 'Koffi Adjovi');

        $seance = $this->creerSeance();
        for ($i = 0; $i < 10; $i++) {
            $this->from('/seances')->post("/seances/{$seance->id}/inscription", ['nom' => "J{$i}x", 'telephone' => '01970000'.str_pad((string) $i, 2, '0', STR_PAD_LEFT), 'statut_scolaire' => 'lyceen'] + $this->champsAntiRobot());
        }
        $this->from('/seances')->post("/seances/{$seance->id}/inscription", ['nom' => 'Trop', 'telephone' => '0197000099', 'statut_scolaire' => 'lyceen'] + $this->champsAntiRobot())
            ->assertRedirect('/seances'.self::ANCRE)
            ->assertSessionHasErrors('formulaire');
    }

    public function test_formation_application_keeps_input_after_error(): void
    {
        $formation = $this->creerFormation();
        $page = "/formations/{$formation->id}";

        $this->from($page)->post("{$page}/candidater", ['nom' => 'Awa Sanni', 'telephone' => 'abc', 'motivation' => 'Très motivée'] + $this->champsAntiRobot())
            ->assertRedirect($page.self::ANCRE)
            ->assertSessionHasErrors('telephone');

        $this->get($page)
            ->assertSee('value="Awa Sanni"', false)
            ->assertSee('Très motivée')
            ->assertSee('role="alert"', false);
    }

    public function test_session_form_reopens_with_input_only_for_the_right_session(): void
    {
        $cible = $this->creerSeance(['titre' => 'Séance cible']);
        $this->creerSeance(['titre' => 'Autre séance']);

        $this->from('/seances')->post("/seances/{$cible->id}/inscription", [
            'nom' => 'Awa Sanni', 'telephone' => '12', 'statut_scolaire' => 'etudiant', 'seance_formulaire' => $cible->id,
        ] + $this->champsAntiRobot())->assertRedirect('/seances'.self::ANCRE);

        $html = $this->get('/seances')->assertOk()->getContent();

        preg_match_all('/<details class="d-inscription" data-seance="(\d+)"\s+open\s*>/', $html, $ouverts);
        $this->assertSame([(string) $cible->id], $ouverts[1], 'Seul le formulaire de la séance concernée est rouvert.');
        $this->assertSame(1, substr_count($html, 'value="Awa Sanni"'), 'Les saisies ne sont recopiées que dans ce formulaire.');
    }
}
