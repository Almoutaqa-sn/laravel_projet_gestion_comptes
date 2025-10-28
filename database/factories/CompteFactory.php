<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompteFactory extends Factory {
    public function definition(): array {
        return [
            'id' => Str::uuid(),
            'numero_compte' => 'CPT-' . strtoupper(Str::random(8)),
            'titulaire' => $this->faker->name(),
            'type' => $this->faker->randomElement(['EPARGNE', 'CHEQUE']),
            'solde' => $this->faker->randomFloat(2, 1000, 100000),
            'devise' => 'XOF',
            'date_creation' => now(),
            'statut' => $this->faker->randomElement(['ACTIF', 'BLOQUE', 'FERME']),
            'version' => 1,
        ];
    }

    public function forClient($clientId) {
        return $this->state(function (array $attributes) use ($clientId) {
            return [
                'client_id' => $clientId,
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
