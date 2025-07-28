<?php

use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\ObisCode;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a single phase meter is calculated correctly', function () {
    $meter = Meter::factory()->singlePhaseElectrical()->create();
    $obisCode = ObisCode::factory()->create();
    $startDate = now()->subMonth()->startOfMonth();
    $endDate = now()->subMonth()->endOfMonth();
    collect([
        ['read_at' => $startDate, 'value' => 1000],
        ['read_at' => $startDate->addDay(), 'value' => 1200],
        ['read_at' => $startDate->addDays(3), 'value' => 1800],
        ['read_at' => $endDate, 'value' => 2000],
        ['read_at' => now()->startOfMonth(), 'value' => 2000],
        ['read_at' => now()->startOfMonth()->addDays(20), 'value' => 4500],
    ])->each(fn ($reading) => MeterReading::factory()->for($meter)->create($reading));

    Product::factory()
        ->for($obisCode)
        ->forPriceable($meter)
        ->withPrice([
            'amount' => 2.50,
            'description' => 'Kilowatt Hour',
        ])->withService($meter->service)
        ->create([
            'recurring' => true,
            'name' => 'Single Phase Electricity',
        ]);

    $lineItems = $meter->lineItemsByDate($startDate, $endDate);

    $this->assertEquals(1, $lineItems->count());
    $this->assertEquals(1000, $lineItems->sum('usage'));
    $this->assertEquals(2500, $lineItems->sum('amount'));

    $lineItems = $meter->lineItemsByDate(now()->startOfMonth(), now()->endOfMonth());

    $this->assertEquals(1, $lineItems->count());
    $this->assertEquals(2500, $lineItems->sum('usage'));
    $this->assertEquals(6250, $lineItems->sum('amount'));
});
