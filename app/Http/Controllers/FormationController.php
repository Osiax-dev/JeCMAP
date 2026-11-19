<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Candidature;
use App\Models\Formation;
use App\Rules\TelephoneValide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::where('statut', 'active')->latest()->get();

        return view('formations.index', compact('formations'));
    }

    public function show(Formation $formation)
    {
        return view('formations.show', compact('formation'));
    }

    public function candidater(Request $request, Formation $formation)
    {
        // Une formation clôturée reste consultable mais n'accepte plus de candidature (même en appelant l'URL directement).
        if ($formation->statut !== 'active') {
            return $this->retourFormulaire()->withInput()->withErrors([
                'formulaire' => 'Cette formation n\'accepte plus de candidatures.',
            ]);
        }

        $validated = $this->validerFormulairePublic($request, [
            'nom' => ['required', 'string', 'min:2', 'max:120'],
            'telephone' => ['required', 'string', new TelephoneValide],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'motivation' => ['nullable', 'string', 'max:2000'],
        ]);

        $dejaCandidat = DB::transaction(function () use ($validated, $formation) {
            $beneficiaire = Beneficiaire::trouverOuCreer($validated);

            $candidature = Candidature::firstOrCreate(
                ['beneficiaire_id' => $beneficiaire->id, 'formation_id' => $formation->id],
                ['motivation' => $validated['motivation'] ?? null, 'statut' => 'en_attente'],
            );

            return ! $candidature->wasRecentlyCreated;
        });

        return $this->retourFormulaire()->with('success', $dejaCandidat
            ? 'Ta candidature pour cette formation est déjà enregistrée. L\'équipe JeCMAP te contactera bientôt.'
            : 'Ta candidature a bien été envoyée ! L\'équipe JeCMAP te contactera bientôt.');
    }
}
