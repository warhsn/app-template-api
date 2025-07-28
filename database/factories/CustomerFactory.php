<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->company(),
            'registration_number' => random_int(10000, 545000),
            'vat_number' => random_int(10000, 545000),
            'user_id' => User::factory(),
            'billing_email' => fake()->email(),
        ];
    }
}
