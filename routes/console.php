<?php

use App\Console\Commands\CheckForWaterLeaks;
use App\Console\Commands\IngestKamstrupMeterReadings;
use App\Console\Commands\SummariseMeters;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(IngestKamstrupMeterReadings::class)->hourly();
Schedule::command(SummariseMeters::class)->hourlyAt('05');
Schedule::command(CheckForWaterLeaks::class)->dailyAt('05:00');
