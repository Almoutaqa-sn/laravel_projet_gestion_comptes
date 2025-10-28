<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminFactory extends Factory {
    public function definition(): array {
        return [
            'id' => Str::uuid(),
            'nom' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->unique()->phoneNumber(),
            'role' => 'admin',
        ];
    }
}
