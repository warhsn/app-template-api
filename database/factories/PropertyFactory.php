<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'PTN - '.random_int(100, 999),
            'square_meters' => random_int(20000, 50000),
            'customer_id' => Customer::factory(),
            'address' => fake()->address(),
            'water_rights' => fake()->boolean(),
            'sub_dividable' => fake()->boolean(),
        ];
    }
}
