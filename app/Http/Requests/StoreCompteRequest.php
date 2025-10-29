<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SenegalesePhone;
use App\Rules\SenegaleseNCI;

class StoreCompteRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true; // Temporairement autorisé pour les tests
    }

    /**
     * Préparer les données pour validation
     */
    protected function prepareForValidation(): void
    {
        // Générer le titulaire à partir des données client si nécessaire
        if ($this->has('client.titulaire')) {
            $this->merge([
                'titulaire' => $this->input('client.titulaire')
            ]);
        }

        // Définir la devise par défaut
        if (!$this->has('devise')) {
            $this->merge(['devise' => 'FCFA']);
        }

        // Convertir type en majuscules
        if ($this->has('type')) {
            $this->merge([
                'type' => strtoupper($this->input('type'))
            ]);
        }
    }

    /**
     * Règles de validation pour créer un compte.
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:CHEQUE,EPARGNE',
            'soldeInitial' => 'required|numeric|min:10000',
            'devise' => 'required|string|in:FCFA,XOF,USD,EUR',
            'client.id' => 'nullable|uuid|exists:clients,id',
            'client.titulaire' => 'required|string|max:255',
            'client.nci' => ['nullable', 'string', new SenegaleseNCI()],
            'client.email' => 'required|email|unique:clients,email',
            'client.telephone' => ['required', 'string', new SenegalesePhone(), 'unique:clients,telephone'],
            'client.adresse' => 'required|string|max:500',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Le type de compte est obligatoire.',
            'type.in' => 'Le type doit être CHEQUE ou EPARGNE.',
            'soldeInitial.required' => 'Le solde initial est obligatoire.',
            'soldeInitial.numeric' => 'Le solde initial doit être un nombre.',
            'soldeInitial.min' => 'Le solde initial doit être d\'au moins 10 000 FCFA.',
            'devise.required' => 'La devise est obligatoire.',
            'devise.in' => 'La devise doit être FCFA, XOF, USD ou EUR.',
            'client.id.uuid' => 'L\'ID du client doit être un UUID valide.',
            'client.id.exists' => 'Le client sélectionné n\'existe pas.',
            'client.titulaire.required' => 'Le nom du titulaire est obligatoire.',
            'client.titulaire.max' => 'Le nom du titulaire ne peut pas dépasser 255 caractères.',
            'client.nci.string' => 'Le NCI doit être une chaîne de caractères.',
            'client.email.required' => 'L\'email est obligatoire.',
            'client.email.email' => 'L\'email doit être valide.',
            'client.email.unique' => 'Cet email est déjà utilisé.',
            'client.telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'client.telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'client.adresse.required' => 'L\'adresse est obligatoire.',
            'client.adresse.max' => 'L\'adresse ne peut pas dépasser 500 caractères.',
        ];
    }

    /**
     * Attributs personnalisés pour les messages d'erreur
     */
    public function attributes(): array
    {
        return [
            'client.titulaire' => 'nom du titulaire',
            'client.email' => 'email',
            'client.telephone' => 'numéro de téléphone',
            'client.adresse' => 'adresse',
            'client.nci' => 'numéro de carte d\'identité',
            'soldeInitial' => 'solde initial',
        ];
    }
}
