<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeOrientation;
use Illuminate\Http\Request;
use App\Mail\ReponseOrientation;
use Illuminate\Support\Facades\Mail;

class DemandeAdminController extends Controller
{
    public function index()
    {
        $demandes = DemandeOrientation::with('beneficiaire')->latest()->paginate(15);
        return view('admin.demandes.index', compact('demandes'));
    }

    public function show(DemandeOrientation $demande)
    {
        $demande->load('beneficiaire.suivis');
        return view('admin.demandes.show', compact('demande'));
    }



public function repondre(Request $request, DemandeOrientation $demande)
{
    $validated = $request->validate([
        'reponse_admin' => 'required|string|max:2000',
    ]);

    $demande->update([
        'reponse_admin' => $validated['reponse_admin'],
        'statut' => 'traitee',
        'traitee_le' => now(),
    ]);

    if ($demande->beneficiaire->email) {
        // Mis en file d'attente : envoyé par le worker, avec nouvelles tentatives en cas d'échec.
        Mail::to($demande->beneficiaire->email)->queue(new ReponseOrientation($demande));
    }

    return back()->with('success', $demande->beneficiaire->email
        ? 'Réponse enregistrée. L\'e-mail au bénéficiaire part en arrière-plan (quelques minutes au plus).'
        : 'Réponse enregistrée (le bénéficiaire n\'a pas d\'email renseigné — contacte-le par téléphone/WhatsApp).');
}
}