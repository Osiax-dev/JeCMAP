<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class Controller
{
    /** Ancre placée juste au-dessus des messages des formulaires publics (succès, erreurs). */
    public const ANCRE_MESSAGES = 'messages-formulaire';

    /** Page précédente + ancre des messages : après un envoi, le navigateur affiche directement le message. */
    public static function urlRetourFormulaire(): string
    {
        return strtok(url()->previous(), '#').'#'.self::ANCRE_MESSAGES;
    }

    protected function retourFormulaire(): RedirectResponse
    {
        return redirect()->to(self::urlRetourFormulaire());
    }

    /**
     * Validation d'un formulaire public : en cas d'erreur, retour sur l'ancre des messages
     * avec les saisies conservées (old()) au lieu d'un retour en haut de page.
     */
    protected function validerFormulairePublic(Request $request, array $regles, array $messages = []): array
    {
        $validator = Validator::make($request->all(), $regles, $messages);

        if ($validator->fails()) {
            throw (new ValidationException($validator))->redirectTo(self::urlRetourFormulaire());
        }

        return $validator->validated();
    }
}
