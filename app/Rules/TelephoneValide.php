<?php

namespace App\Rules;

use App\Models\Beneficiaire;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Numéro de téléphone plausible : chiffres, espaces, + ( ) . - autorisés, au moins 8 chiffres. */
class TelephoneValide implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)
            || ! preg_match('/^\+?[0-9\s().-]{8,25}$/', $value)
            || Beneficiaire::normaliserTelephone($value) === null) {
            $fail('Le numéro de téléphone n\'est pas valide (au moins 8 chiffres).');
        }
    }
}
