<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protection anti-robot des formulaires publics, sans service externe ni JavaScript :
 *  1. Pot de miel : le champ `site_internet` est invisible pour un humain ; s'il est rempli, c'est un robot.
 *  2. Délai minimal : `_debut_formulaire` contient l'heure d'affichage (chiffrée par Laravel).
 *     Un envoi plus rapide que DELAI_MIN_SECONDES ou un jeton absent/falsifié/expiré est refusé.
 *
 * Les champs sont générés par le composant Blade <x-anti-robot />.
 */
class ProtectionAntiRobot
{
    public const CHAMP_POT_DE_MIEL = 'site_internet';

    public const CHAMP_DEBUT = '_debut_formulaire';

    public const DELAI_MIN_SECONDES = 3;

    public const VALIDITE_MAX_SECONDES = 86400; // 24 h

    public const MESSAGE = 'Envoi refusé par la protection anti-robot. Recharge la page, remplis le formulaire puis réessaie.';

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->estSuspect($request)) {
            return redirect()->to(Controller::urlRetourFormulaire())
                ->withInput($request->except([self::CHAMP_POT_DE_MIEL, self::CHAMP_DEBUT]))
                ->withErrors(['formulaire' => self::MESSAGE]);
        }

        return $next($request);
    }

    private function estSuspect(Request $request): bool
    {
        if (filled($request->input(self::CHAMP_POT_DE_MIEL))) {
            return true;
        }

        $jeton = $request->input(self::CHAMP_DEBUT);

        if (! is_string($jeton) || $jeton === '') {
            return true;
        }

        try {
            $debut = (int) Crypt::decryptString($jeton);
        } catch (DecryptException) {
            return true;
        }

        $ecoule = now()->timestamp - $debut;

        return $ecoule < self::DELAI_MIN_SECONDES || $ecoule > self::VALIDITE_MAX_SECONDES;
    }
}
