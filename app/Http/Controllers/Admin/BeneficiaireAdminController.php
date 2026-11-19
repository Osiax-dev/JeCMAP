<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiaire;
use Illuminate\Http\Request;

class BeneficiaireAdminController extends Controller
{
    public function index()
    {
        $beneficiaires = Beneficiaire::withCount(['demandesOrientation', 'candidatures', 'inscriptionsSeances'])
            ->latest()
            ->paginate(20);
        return view('admin.beneficiaires.index', compact('beneficiaires'));
    }

    public function show(Beneficiaire $beneficiaire)
    {
        $beneficiaire->load([
            'demandesOrientation',
            'candidatures.formation',
            'inscriptionsSeances.seance',
            'suivis.auteur',
        ]);
        return view('admin.beneficiaires.show', compact('beneficiaire'));
    }

    public function ajouterSuivi(Request $request, Beneficiaire $beneficiaire)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $beneficiaire->suivis()->create([
            'user_id' => auth()->id(),
            'note' => $validated['note'],
        ]);

        return back()->with('success', 'Note ajoutée.');
    }
}