<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\InscriptionSeance;
use App\Models\SeanceOrientation;
use App\Rules\TelephoneValide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeanceOrientationController extends Controller
{
    public function index()
    {
        // Les séances dont la date est dépassée ne sont plus proposées, même si leur statut n'a pas été mis à jour.
        $seances = SeanceOrientation::ouvertes()
            ->withCount('inscriptions')
            ->orderBy('date_seance')
            ->get();

        return view('seances.index', compact('seances'));
    }

    public function inscrire(Request $request, SeanceOrientation $seance)
    {
        if (! $seance->inscriptionsOuvertes()) {
            return $this->retourFormulaire()->withInput()->withErrors([
                'formulaire' => 'Les inscriptions à cette séance sont fermées.',
            ]);
        }

        $validated = $this->validerFormulairePublic($request, [
            'nom' => ['required', 'string', 'min:2', 'max:120'],
            'telephone' => ['required', 'string', new TelephoneValide],
            'statut_scolaire' => ['required', 'in:collegien,lyceen,etudiant,sans_emploi,autre'],
        ]);

        $resultat = DB::transaction(function () use ($validated, $seance) {
            // Verrou sur la ligne de la séance : deux inscriptions simultanées sont traitées l'une après l'autre,
            // la seconde voit donc le bon nombre d'inscrits (pas de dépassement de capacité).
            $seance = SeanceOrientation::whereKey($seance->getKey())->lockForUpdate()->firstOrFail();

            if (! $seance->inscriptionsOuvertes()) {
                return 'fermee';
            }

            $beneficiaire = Beneficiaire::trouver($validated);

            if ($beneficiaire && InscriptionSeance::where('seance_id', $seance->id)->where('beneficiaire_id', $beneficiaire->id)->exists()) {
                return 'deja_inscrit';
            }

            if ($seance->places !== null && $seance->inscriptions()->count() >= $seance->places) {
                return 'complet';
            }

            $beneficiaire = Beneficiaire::trouverOuCreer($validated);

            InscriptionSeance::create([
                'seance_id' => $seance->id,
                'beneficiaire_id' => $beneficiaire->id,
                'statut' => 'inscrit',
            ]);

            return 'inscrit';
        });

        return match ($resultat) {
            'inscrit' => $this->retourFormulaire()->with('success', 'Inscription confirmée ! On t\'attend à la séance.'),
            'deja_inscrit' => $this->retourFormulaire()->with('success', 'Tu es déjà inscrit à cette séance. On t\'attend !'),
            'complet' => $this->retourFormulaire()->withInput()->withErrors([
                'formulaire' => 'Désolé, cette séance est complète. Choisis une autre date ou écris-nous sur WhatsApp.',
            ]),
            default => $this->retourFormulaire()->withInput()->withErrors([
                'formulaire' => 'Les inscriptions à cette séance sont fermées.',
            ]),
        };
    }
}
