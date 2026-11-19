<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\Request;

class FormationAdminController extends Controller
{
    public function index()
    {
        $formations = Formation::withCount('candidatures')->latest()->get();
        return view('admin.formations.index', compact('formations'));
    }

    public function create()
    {
        return view('admin.formations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_jours' => 'nullable|integer|min:1|max:3650',
            'prerequis' => 'nullable|string',
            'places' => 'nullable|integer|min:1|max:100000',
            'photo' => 'nullable|image|max:2048',
        ]);
        $validated['created_by'] = auth()->id();
        $validated['statut'] = 'active';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('formations', 'public');
        }

        Formation::create($validated);
        return redirect()->route('admin.formations.index')->with('success', 'Formation créée.');
    }

    /** Détail d'une formation et liste de ses candidatures (route resource « show », absente auparavant : erreur 500). */
    public function show(Formation $formation)
    {
        $formation->load(['candidatures' => fn ($q) => $q->latest(), 'candidatures.beneficiaire']);

        return view('admin.formations.show', compact('formation'));
    }

    public function edit(Formation $formation)
    {
        return view('admin.formations.edit', compact('formation'));
    }

public function update(Request $request, Formation $formation)
{
    $validated = $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'nullable|string',
        'duree_jours' => 'nullable|integer|min:1|max:3650',
        'prerequis' => 'nullable|string',
        'places' => 'nullable|integer|min:1|max:100000',
        'statut' => 'required|in:active,cloturee',
        'photo' => 'nullable|image|max:2048',
    ]);


    // ... reste du code
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('formations', 'public');
        }

        $formation->update($validated);
        return redirect()->route('admin.formations.index')->with('success', 'Formation mise à jour.');
    }

    public function destroy(Formation $formation)
    {
        // La suppression effacerait aussi les candidatures (cascade) : on la refuse dans ce cas.
        if ($formation->candidatures()->exists()) {
            return back()->withErrors([
                'formulaire' => 'Cette formation a des candidatures : la supprimer les effacerait aussi. Passe-la plutôt en « Clôturée ».',
            ]);
        }

        $formation->delete();
        return back()->with('success', 'Formation supprimée.');
    }
}