<?php

namespace App\Mail\Concerns;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Règles communes des e-mails envoyés en arrière-plan (file d'attente « database »).
 *
 * - 4 tentatives au total, espacées de 1 min, 5 min puis 15 min (panne SMTP passagère, quota Gmail…).
 * - Envoi seulement après validation de la transaction en base : appeler $this->afterCommit() dans le constructeur.
 * - Après le dernier échec, le job part dans la table failed_jobs
 *   (php artisan queue:failed / queue:retry all) et un avertissement est journalisé, sans données personnelles.
 */
trait EnvoiEnFileAttente
{
    public int $tries = 4;

    /** @return array<int, int> secondes d'attente avant chaque nouvelle tentative */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function failed(Throwable $exception): void
    {
        Log::warning('Échec définitif d\'envoi d\'e-mail : '.static::class, [
            'demande_id' => $this->demande->id ?? null,
            'erreur' => $exception->getMessage(),
        ]);
    }
}
