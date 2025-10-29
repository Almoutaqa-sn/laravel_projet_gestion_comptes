<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SenegalesePhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Formats acceptés pour les numéros sénégalais :
        // +221771234567, +221761234567, +221701234567, +221781234567, +221331234567
        // 771234567, 761234567, 701234567, 781234567, 331234567

        // Nettoyer le numéro (supprimer espaces et tirets)
        $cleanNumber = preg_replace('/[\s\-]/', '', $value);

        // Vérifier si c'est un numéro sénégalais
        if (!preg_match('/^(?:\+221|0)?([76]\d{7}|33\d{7})$/', $cleanNumber)) {
            $fail('Le numéro de téléphone doit être un numéro sénégalais valide (ex: +221771234567 ou 771234567).');
            return;
        }

        // Vérifier la longueur totale
        if (strlen($cleanNumber) < 9 || strlen($cleanNumber) > 13) {
            $fail('Le numéro de téléphone doit contenir entre 9 et 13 chiffres.');
            return;
        }
    }
}
