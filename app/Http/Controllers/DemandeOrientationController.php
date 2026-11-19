<?php

namespace App\Http\Controllers;

use App\Mail\NouvelleDemandeOrientation;
use App\Models\Beneficiaire;
use App\Models\DemandeOrientation;
use App\Rules\TelephoneValide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DemandeOrientationController extends Controller
{
    public function create()
    {
        return view('orientation.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validerFormulairePublic($request, [
            'nom' => ['required', 'string', 'min:2', 'max:120'],
            // Au moins un moyen de contact, sinon l'équipe ne peut pas répondre.
            'telephone' => ['nullable', 'required_without:email', 'string', new TelephoneValide],
            'email' => ['nullable', 'required_without:telephone', 'email:rfc', 'max:255'],
            'statut_scolaire' => ['required', 'in:collegien,lyceen,etudiant,sans_emploi,autre'],
            'niveau' => ['nullable', 'string', 'max:100'],
            'besoin' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'telephone.required_without' => 'Indique un téléphone ou un e-mail pour que l\'équipe puisse te répondre.',
            'email.required_without' => 'Indique un téléphone ou un e-mail pour que l\'équipe puisse te répondre.',
        ]);

        $demande = DB::transaction(function () use ($validated) {
            $beneficiaire = Beneficiaire::trouverOuCreer($validated);

            // Anti-doublon : une seule demande en attente par bénéficiaire.
            if ($beneficiaire->demandesOrientation()->where('statut', 'nouvelle')->exists()) {
                return null;
            }

            return DemandeOrientation::create([
                'beneficiaire_id' => $beneficiaire->id,
                'besoin' => $validated['besoin'],
                'statut' => 'nouvelle',
            ]);
        });

        if (! $demande) {
            return $this->retourFormulaire()->withInput()->withErrors([
                'formulaire' => 'Tu as déjà une demande en cours de traitement. L\'équipe JeCMAP va te contacter ; pour ajouter une précision, écris-nous sur WhatsApp.',
            ]);
        }

        $adresseAdmin = config('mail.jecmap_admin');

        if (filled($adresseAdmin)) {
            // Mis en file d'attente : une panne SMTP ne fait plus échouer la demande.
            Mail::to($adresseAdmin)->queue(new NouvelleDemandeOrientation($demande));
        } else {
            Log::warning('MAIL_JECMAP_ADMIN non configuré : notification de nouvelle demande non envoyée.', ['demande_id' => $demande->id]);
        }

        return $this->retourFormulaire()->with('success', 'Ta demande a bien été envoyée. L\'équipe JeCMAP te contactera bientôt.');
    }
}
