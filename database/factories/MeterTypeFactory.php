<?php

namespace Database\Factories;

use App\Enums\Unit;
use App\Models\MeterType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeterType>
 */
class MeterTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => MeterType::SINGLE_PHASE_SMART_METER,
            'unit' => Unit::KILOWATT_HOUR,
        ];
    }
}
