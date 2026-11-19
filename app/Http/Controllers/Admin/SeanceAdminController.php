<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeanceOrientation;
use Illuminate\Http\Request;

class SeanceAdminController extends Controller
{
    public function index()
    {
        $seances = SeanceOrientation::withCount('inscriptions')->orderBy('date_seance', 'desc')->get();
        return view('admin.seances.index', compact('seances'));
    }

    public function create()
    {
        return view('admin.seances.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_seance' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'format' => 'required|in:presentiel,en_ligne',
            'places' => 'nullable|integer|min:1|max:100000',
            'photo' => 'nullable|image|max:2048',
        ]);
        $validated['created_by'] = auth()->id();
        $validated['statut'] = 'a_venir';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('seances', 'public');
        }

        SeanceOrientation::create($validated);
        return redirect()->route('admin.seances.index')->with('success', 'Séance créée.');
    }

    public function show(SeanceOrientation $seance)
    {
        $seance->load('inscriptions.beneficiaire');
        return view('admin.seances.show', compact('seance'));
    }

    public function edit(SeanceOrientation $seance)
    {
        return view('admin.seances.edit', compact('seance'));
    }

    public function update(Request $request, SeanceOrientation $seance)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_seance' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'format' => 'required|in:presentiel,en_ligne',
            // On ne peut pas descendre sous le nombre de personnes déjà inscrites.
            'places' => 'nullable|integer|max:100000|min:'.max(1, $seance->inscriptions()->count()),
            'statut' => 'required|in:a_venir,passee',
            'photo' => 'nullable|image|max:2048',
        ], [
            'places.min' => 'Le nombre de places ne peut pas être inférieur au nombre d\'inscrits (:min).',
        ]);


        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('seances', 'public');
        }

        $seance->update($validated);
        return redirect()->route('admin.seances.index')->with('success', 'Séance mise à jour.');
    }

    public function destroy(SeanceOrientation $seance)
    {
        // La suppression effacerait aussi les inscriptions (cascade) : on la refuse dans ce cas.
        if ($seance->inscriptions()->exists()) {
            return back()->withErrors([
                'formulaire' => 'Cette séance a des inscrits : la supprimer les effacerait aussi. Passe-la plutôt en « Passée ».',
            ]);
        }

        $seance->delete();
        return back()->with('success', 'Séance supprimée.');
    }
}