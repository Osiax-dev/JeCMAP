<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeOrientation;
use App\Models\Candidature;
use App\Models\Formation;
use App\Models\SeanceOrientation;

class DashboardController extends Controller
{
    public function index()
    {
        $demandesEnAttente = DemandeOrientation::where('statut', 'nouvelle')->count();
        $candidaturesRecentes = Candidature::with(['beneficiaire', 'formation'])->latest()->take(5)->get();
        $formationsActives = Formation::where('statut', 'active')->count();
        $seancesAVenir = SeanceOrientation::where('statut', 'a_venir')->orderBy('date_seance')->take(5)->get();

        return view('admin.dashboard', compact(
            'demandesEnAttente', 'candidaturesRecentes', 'formationsActives', 'seancesAVenir'
        ));
    }
}