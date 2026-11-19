<?php

/*
 * Messages de validation en français (règles utilisées par JeCMAP et règles courantes).
 * Toute règle absente d'ici retombe sur le message anglais de Laravel.
 */
return [
    'accepted' => 'Le champ :attribute doit être accepté.',
    'array' => 'Le champ :attribute est invalide.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe actuel est incorrect.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'exists' => 'La valeur du champ :attribute est invalide.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'image' => 'Le champ :attribute doit être une image (jpg, png, webp…).',
    'in' => 'La valeur du champ :attribute est invalide.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'lowercase' => 'Le champ :attribute doit être en minuscules.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max éléments.',
        'file' => 'Le fichier :attribute ne doit pas dépasser :max Ko.',
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
        'file' => 'Le fichier :attribute doit faire au moins :min Ko.',
        'numeric' => 'Le champ :attribute doit être au moins égal à :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit contenir au moins une majuscule et une minuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Ce mot de passe est apparu dans une fuite de données. Choisis-en un autre.',
    ],
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_with' => 'Le champ :attribute est obligatoire quand :values est rempli.',
    'required_without' => 'Le champ :attribute est obligatoire quand :values n\'est pas rempli.',
    'string' => 'Le champ :attribute doit être un texte.',
    'unique' => 'Cette valeur de :attribute est déjà utilisée.',
    'uploaded' => 'Le fichier :attribute n\'a pas pu être envoyé.',

    'custom' => [],

    // Noms lisibles des champs dans les messages.
    'attributes' => [
        'nom' => 'nom et prénoms',
        'name' => 'nom',
        'telephone' => 'téléphone',
        'email' => 'e-mail',
        'statut_scolaire' => 'statut',
        'niveau' => 'niveau',
        'besoin' => 'description de ta situation',
        'motivation' => 'motivation',
        'titre' => 'titre',
        'description' => 'description',
        'duree_jours' => 'durée (jours)',
        'prerequis' => 'prérequis',
        'places' => 'places',
        'statut' => 'statut',
        'photo' => 'photo',
        'date_seance' => 'date et heure',
        'lieu' => 'lieu',
        'format' => 'format',
        'note' => 'note',
        'reponse_admin' => 'réponse',
        'password' => 'mot de passe',
        'current_password' => 'mot de passe actuel',
    ],
];
