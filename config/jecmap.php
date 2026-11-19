<?php

/*
 * Réglages propres à JeCMAP.
 */
return [

    // Limite d'envoi des formulaires publics par adresse IP (orientation, candidature, inscription).
    // À relever ponctuellement si une classe entière s'inscrit depuis le même Wi-Fi.
    'limite_formulaires' => [
        'par_minute' => (int) env('JECMAP_LIMITE_FORMULAIRES_MINUTE', 10),
        'par_heure' => (int) env('JECMAP_LIMITE_FORMULAIRES_HEURE', 60),
    ],

];
