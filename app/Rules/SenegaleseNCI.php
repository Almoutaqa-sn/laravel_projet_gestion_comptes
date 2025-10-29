<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SenegaleseNCI implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Format du NCI sénégalais : 13 caractères alphanumériques
        // Exemples : 1234567890123, ABC1234567890

        // Nettoyer la valeur (supprimer espaces)
        $cleanNCI = trim($value);

        // Vérifier la longueur
        if (strlen($cleanNCI) !== 13) {
            $fail('Le numéro de carte d\'identité nationale doit contenir exactement 13 caractères.');
            return;
        }

        // Vérifier que c'est alphanumérique
        if (!preg_match('/^[A-Z0-9]{13}$/i', $cleanNCI)) {
            $fail('Le numéro de carte d\'identité nationale doit contenir uniquement des lettres et des chiffres.');
            return;
        }

        // Format typique : commence souvent par des chiffres
        if (!preg_match('/^\d/', $cleanNCI)) {
            $fail('Le numéro de carte d\'identité nationale doit commencer par un chiffre.');
            return;
        }
    }
}
