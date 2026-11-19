<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Beneficiaire extends Model
{
    protected $fillable = ['nom', 'telephone', 'telephone_normalise', 'email', 'commune', 'statut_scolaire', 'niveau'];

    public function demandesOrientation() { return $this->hasMany(DemandeOrientation::class); }
    public function inscriptionsSeances() { return $this->hasMany(InscriptionSeance::class); }
    public function candidatures() { return $this->hasMany(Candidature::class); }
    public function suivis() { return $this->hasMany(Suivi::class); }

    /**
     * Chiffres seuls, sans 00 ni indicatif 229 : "+229 01 96 88 81 91" et "0196888191" donnent la même valeur.
     * Retourne null si moins de 8 chiffres.
     */
    public static function normaliserTelephone(?string $telephone): ?string
    {
        $chiffres = preg_replace('/\D+/', '', (string) $telephone);
        $chiffres = preg_replace('/^00/', '', $chiffres);

        if (str_starts_with($chiffres, '229') && strlen($chiffres) >= 11) {
            $chiffres = substr($chiffres, 3);
        }

        return strlen($chiffres) >= 8 ? $chiffres : null;
    }

    /** Nom comparable : minuscules, sans accents, espaces réduits. */
    public static function normaliserNom(?string $nom): string
    {
        return Str::lower(Str::squish(Str::ascii((string) $nom)));
    }

    /**
     * Retrouve le bénéficiaire existant (même téléphone ou même e-mail ET même nom), sans rien créer.
     *
     * Le nom fait partie de la correspondance car, au Bénin, plusieurs jeunes d'une même
     * famille partagent souvent un téléphone : on ne fusionne pas deux personnes différentes.
     */
    public static function trouver(array $donnees): ?self
    {
        $telephone = self::normaliserTelephone($donnees['telephone'] ?? null);
        $email = filled($donnees['email'] ?? null) ? Str::lower(trim($donnees['email'])) : null;
        $nom = self::normaliserNom($donnees['nom'] ?? '');

        $memeNom = fn (self $b) => self::normaliserNom($b->nom) === $nom;

        $existant = null;

        if ($telephone) {
            $existant = self::where('telephone_normalise', $telephone)->get()->first($memeNom);
        }

        if (! $existant && $email) {
            $existant = self::where('email', $email)->get()->first($memeNom);
        }

        return $existant;
    }

    /**
     * Retrouve le bénéficiaire (voir trouver()) ou le crée.
     * Un bénéficiaire existant est seulement complété (champs vides), jamais écrasé.
     */
    public static function trouverOuCreer(array $donnees): self
    {
        $telephone = self::normaliserTelephone($donnees['telephone'] ?? null);
        $email = filled($donnees['email'] ?? null) ? Str::lower(trim($donnees['email'])) : null;
        $existant = self::trouver($donnees);

        if (! $existant) {
            return self::create([
                'nom' => Str::squish($donnees['nom']),
                'telephone' => $donnees['telephone'] ?? null,
                'telephone_normalise' => $telephone,
                'email' => $email,
                'statut_scolaire' => $donnees['statut_scolaire'] ?? null,
                'niveau' => $donnees['niveau'] ?? null,
            ]);
        }

        $complements = [
            'telephone' => $donnees['telephone'] ?? null,
            'telephone_normalise' => $telephone,
            'email' => $email,
            'statut_scolaire' => $donnees['statut_scolaire'] ?? null,
            'niveau' => $donnees['niveau'] ?? null,
        ];

        foreach ($complements as $champ => $valeur) {
            if (blank($existant->{$champ}) && filled($valeur)) {
                $existant->{$champ} = $valeur;
            }
        }

        $existant->save();

        return $existant;
    }
}
