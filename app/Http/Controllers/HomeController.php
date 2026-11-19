<?php

namespace App\Http\Controllers;

use App\Models\SeanceOrientation;

class HomeController extends Controller
{
    public function index()
    {
        // Prochaine séance encore ouverte (statut « à venir » et date future), avec le nombre d'inscrits.
        $prochaineSeance = SeanceOrientation::ouvertes()
            ->withCount('inscriptions')
            ->orderBy('date_seance')
            ->first();

        return view('home', compact('prochaineSeance'));
    }

    public function apropos()
    {
        return view('apropos');
    }
}
