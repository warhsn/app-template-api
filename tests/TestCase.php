<?php

namespace Tests;

use App\Models\Meter;
use App\Models\ObisCode;
use App\Models\Product;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function tieredDomesticWaterProduct(ObisCode $obisCode, Meter $meter): Product
    {
        return Product::factory()
            ->for($obisCode)
            ->forPriceable($meter)
            ->withPrice(['amount' => 0, 'description' => '< 10Kl', 'tier_limit' => 10])
            ->withPrice(['amount' => 24.13, 'description' => '> 10 - 30 kL', 'tier_limit' => 30])
            ->withPrice(['amount' => 31.57, 'description' => '> 30 - 40 KL', 'tier_limit' => 40])
            ->withPrice(['amount' => 50.52, 'description' => '> 40 - 50 KL', 'tier_limit' => 50])
            ->withPrice(['amount' => 63.3, 'description' => '> 50KL', 'tier_limit' => 50])
            ->withService($meter->service)
            ->create([
                'recurring' => true,
                'tiered' => true,
                'name' => 'Domestic Water',
            ]);
    }

    public function domesticWaterObis(): ObisCode
    {
        return ObisCode::factory()->create([
            'name' => 'Domestic Water',
            'code' => Meter::CUMULATIVE_WATER_OBIS,
        ]);
    }

    public function domesticWaterMeter(): Meter
    {
        return Meter::factory()->domesticWater()->create();
    }
}
