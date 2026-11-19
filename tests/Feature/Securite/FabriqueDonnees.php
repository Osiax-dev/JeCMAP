<?php

namespace Tests\Feature\Securite;

use App\Models\Beneficiaire;
use App\Models\DemandeOrientation;
use App\Models\Formation;
use App\Models\SeanceOrientation;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

/** Données et champs communs aux tests de sécurité. */
trait FabriqueDonnees
{
    protected function creerAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function creerFormation(array $attributs = []): Formation
    {
        return Formation::create(array_merge([
            'titre' => 'Développement web',
            'statut' => 'active',
            'created_by' => User::factory()->admin()->create()->id,
        ], $attributs));
    }

    protected function creerSeance(array $attributs = []): SeanceOrientation
    {
        return SeanceOrientation::create(array_merge([
            'titre' => 'Choisir sa filière',
            'date_seance' => now()->addWeek(),
            'format' => 'presentiel',
            'statut' => 'a_venir',
            'created_by' => User::factory()->admin()->create()->id,
        ], $attributs));
    }

    protected function creerBeneficiaireAvecDemande(): Beneficiaire
    {
        $beneficiaire = Beneficiaire::create([
            'nom' => 'Awa Sanni',
            'telephone' => '+229 01 97 00 00 01',
            'telephone_normalise' => '0197000001',
            'email' => 'awa@example.com',
            'statut_scolaire' => 'lyceen',
        ]);

        DemandeOrientation::create([
            'beneficiaire_id' => $beneficiaire->id,
            'besoin' => 'Je ne sais pas quelle filière choisir après le BAC.',
            'statut' => 'nouvelle',
        ]);

        return $beneficiaire;
    }

    /** Champs anti-robot valides : pot de miel vide, formulaire affiché il y a 10 secondes. */
    protected function champsAntiRobot(int $secondesDepuisAffichage = 10): array
    {
        return [
            'site_internet' => '',
            '_debut_formulaire' => Crypt::encryptString((string) (now()->timestamp - $secondesDepuisAffichage)),
        ];
    }
}
