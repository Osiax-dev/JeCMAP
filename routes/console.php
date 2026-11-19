<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Envoi des e-mails en file d'attente (driver « database »).
 * Pour un hébergement sans processus permanent (mutualisé, XAMPP) : une tâche cron/planifiée
 * lance `php artisan schedule:run` chaque minute, qui traite la file puis s'arrête.
 * Sur un VPS, préférer un worker permanent (`php artisan queue:work`) géré par Supervisor : voir DEPLOIEMENT.md.
 */
Schedule::command('queue:work --stop-when-empty --tries=4 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping()
    ->name('envoi-file-attente');
