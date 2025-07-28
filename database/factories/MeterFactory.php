<?php

namespace Database\Factories;

use App\Enums\Unit;
use App\Models\Customer;
use App\Models\MeterType;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meter>
 */
class MeterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'serial_number' => random_int(20000, 40000),
            'meter_type_id' => MeterType::factory(),
            'customer_id' => Customer::factory(),
            'property_id' => Property::factory(),
            'service_id' => Service::factory(),
            'lat' => fake()->latitude(),
            'lng' => fake()->longitude(),
            'manual_capture' => fake()->boolean(),
        ];
    }

    public function singlePhaseElectrical()
    {
        return $this->state(function (array $attributes) {
            $meterType = MeterType::firstOrCreate(
                ['name' => MeterType::SINGLE_PHASE_SMART_METER],
                ['unit' => Unit::KILOWATT_HOUR]
            );

            $service = Service::firstOrCreate(
                ['name' => MeterType::SINGLE_PHASE_SMART_METER]
            );

            return [
                'meter_type_id' => $meterType->id,
                'service_id' => $service->id,
            ];
        });
    }

    public function domesticWater()
    {
        return $this->state(function (array $attributes) {
            $meterType = MeterType::firstOrCreate(
                ['name' => MeterType::DOMESTIC_SMART_WATER_METER],
                ['unit' => Unit::CUBMIC_METERS]
            );

            $service = Service::firstOrCreate(
                ['name' => MeterType::DOMESTIC_SMART_WATER_METER]
            );

            return [
                'meter_type_id' => $meterType->id,
                'service_id' => $service->id,
            ];
        });
    }
}
