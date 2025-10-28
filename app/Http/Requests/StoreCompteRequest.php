<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompteRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        // Ici on autorise que les admins créent un compte
        return auth()->check() && auth()->user()->is_admin; 
        // ou simplement true si tu n'as pas encore de gestion de rôle
    }

    /**
     * Règles de validation pour créer ou mettre à jour un compte.
     */
    public function rules(): array
    {
        return [
            'numero_compte' => 'required|string|unique:comptes,numero_compte|max:20',
            'titulaire' => 'required|string|max:255',
            'type' => 'required|string|in:EPARGNE,CHEQUE',
            'solde' => 'required|numeric|min:0',
            'devise' => 'required|string|size:3', // ex: XOF, USD
            'statut' => 'required|string|in:ACTIF,BLOQUE,FERME',
            'client_id' => 'required|uuid|exists:clients,id',
            'admin_id' => 'required|uuid|exists:admins,id',
        ];
    }

    /**
     * Messages d'erreur personnalisés (optionnel)
     */
    public function messages(): array
    {
        return [
            'numero_compte.required' => 'Le numéro de compte est obligatoire.',
            'numero_compte.unique' => 'Ce numéro de compte existe déjà.',
            'titulaire.required' => 'Le titulaire du compte est obligatoire.',
            'type.required' => 'Le type de compte est obligatoire.',
            'type.in' => 'Le type doit être EPARGNE ou CHEQUE.',
            'solde.required' => 'Le solde initial est obligatoire.',
            'solde.numeric' => 'Le solde doit être un nombre.',
            'solde.min' => 'Le solde ne peut pas être négatif.',
            'devise.required' => 'La devise est obligatoire.',
            'devise.size' => 'La devise doit comporter 3 caractères.',
            'statut.required' => 'Le statut du compte est obligatoire.',
            'statut.in' => 'Le statut doit être ACTIF, BLOQUE ou FERME.',
            'client_id.required' => 'Le client associé est obligatoire.',
            'client_id.exists' => 'Le client sélectionné n’existe pas.',
            'admin_id.required' => 'L’administrateur est obligatoire.',
            'admin_id.exists' => 'L’administrateur sélectionné n’existe pas.',
        ];
    }
}
