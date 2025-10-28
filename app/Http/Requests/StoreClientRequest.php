<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        // true = autorise tout le monde, tu peux restreindre plus tard si nécessaire
        return true;
    }

    /**
     * Règles de validation pour créer ou mettre à jour un client.
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'required|string|min:9|max:15|unique:clients,telephone',
            'adresse' => 'required|string|max:500',
        ];
    }

    /**
     * Messages d'erreur personnalisés (optionnel)
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L’email est obligatoire.',
            'email.email' => 'L’email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'adresse.required' => 'L’adresse est obligatoire.',
        ];
    }
}
