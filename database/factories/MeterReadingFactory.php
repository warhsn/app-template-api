<?php

namespace Database\Factories;

use App\Enums\Unit;
use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeterReading>
 */
class MeterReadingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'value' => random_int(1000, 50000),
            'meter_id' => Meter::factory(),
            'obis_code' => Meter::CUMULATIVE_ELECTRICAL_OBIS,
            'read_at' => fake()->dateTime(),
            'unit' => Unit::KILOWATT_HOUR,
            'photo' => null,
        ];
    }
}
