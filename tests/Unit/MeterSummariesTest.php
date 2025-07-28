<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('water meter summaries are calculated correctly', function () {
    $meter = $this->domesticWaterMeter();
    $obisCode = $this->domesticWaterObis();
    $product = $this->tieredDomesticWaterProduct($obisCode, $meter);
    $startDate = now()->subDays(2)->startOfDay();

    foreach (range(2, 1) as $range) {
        dd($range);
    }
});
