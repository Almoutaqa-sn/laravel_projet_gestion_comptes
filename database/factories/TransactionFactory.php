<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory {
    public function definition(): array {
        return [
            'id' => Str::uuid(),
            'type' => $this->faker->randomElement(['DEPOT', 'RETRAIT', 'VIREMENT', 'FRAIS']),
            'montant' => $this->faker->randomFloat(2, 1000, 50000),
            'devise' => 'XOF',
            'description' => $this->faker->sentence(),
            'date_transaction' => now(),
            'statut' => $this->faker->randomElement(['EN_ATTENTE', 'VALIDEE', 'ANNULEE']),
        ];
    }

    public function forCompte($compteId) {
        return $this->state(function (array $attributes) use ($compteId) {
            return [
                'compte_id' => $compteId,
            ];
        });
    }

    public function forAdmin($adminId) {
        return $this->state(function (array $attributes) use ($adminId) {
            return [
                'admin_id' => $adminId,
            ];
        });
    }
}
