<?php

namespace Database\Factories;

use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ObisCode>
 */
class ObisCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Single Phase Electricity',
            'code' => Meter::CUMULATIVE_ELECTRICAL_OBIS,
        ];
    }
}
