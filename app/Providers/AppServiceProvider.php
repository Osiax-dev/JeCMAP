<?php

namespace App\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurerLimitesFormulairesPublics();
    }

    /**
     * Limite d'envoi des formulaires publics (orientation, candidature, inscription séance), par adresse IP.
     * Les seuils restent larges : pendant une séance, toute une classe peut s'inscrire
     * depuis le même Wi-Fi (même IP). Seuils réglables dans config/jecmap.php (.env).
     * Le pot de miel et le dédoublonnage complètent la protection.
     */
    private function configurerLimitesFormulairesPublics(): void
    {
        RateLimiter::for('formulaires-publics', function (Request $request) {
            $reponse = fn () => redirect()->to(Controller::urlRetourFormulaire())
                ->withInput($request->except(['site_internet', '_debut_formulaire']))
                ->withErrors(['formulaire' => 'Trop d\'envois depuis cette connexion. Merci de patienter quelques minutes avant de réessayer.']);

            return [
                Limit::perMinute(config('jecmap.limite_formulaires.par_minute'))->by('min|'.$request->ip())->response($reponse),
                Limit::perHour(config('jecmap.limite_formulaires.par_heure'))->by('heure|'.$request->ip())->response($reponse),
            ];
        });
    }
}
