<?php

use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\ObisCode;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a tiered domestic meter is calculated correctly', function () {
    $meter = Meter::factory()->domesticWater()->create();
    $obisCode = ObisCode::factory()->create([
        'name' => 'Domestic Water',
        'code' => Meter::CUMULATIVE_WATER_OBIS,
    ]);
    $startDate = now()->subMonth()->startOfMonth();
    $endDate = now()->subMonth()->endOfMonth();
    collect([
        ['read_at' => $startDate, 'value' => 5, 'obis_code' => Meter::CUMULATIVE_WATER_OBIS],
        ['read_at' => $endDate, 'value' => 73, 'obis_code' => Meter::CUMULATIVE_WATER_OBIS],
    ])->each(fn ($reading) => MeterReading::factory()->for($meter)->create($reading));

    $this->tieredDomesticWaterProduct($obisCode, $meter);

    $lineItems = $meter->lineItemsByDate($startDate, $endDate);
    $this->assertEquals(Product::first()->prices()->count(), $lineItems->count());
    $this->assertEquals(68, $lineItems->sum('usage'));
    $this->assertEquals(2442.9, $lineItems->sum('amount'));
    $this->assertEquals(0, $lineItems->first()['amount']);
    $this->assertEquals(482.60, $lineItems[1]['amount']);
    $this->assertEquals(315.70, $lineItems[2]['amount']);
    $this->assertEquals(505.20, $lineItems[3]['amount']);
    $this->assertEquals(1139.40, $lineItems[4]['amount']);
});
