<?php

namespace App\Models\Traits;

use App\Models\Product;
use App\Services\MeterSpendCalculator;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait CalculatesMeters
{
    public function firstReadingByObisAndDate(string $obis, Carbon $date): float
    {
        $reading = $this->dailyMeterReadings()
            ->where('obis_code', $obis)
            ->where('read_date', $date->format('Y-m-d'))
            ->orderBy('first_reading_at', 'DESC')
            ->first();

        return $reading?->opening ?? 0;
    }

    public function lastReadingByObisAndDate(?string $obis, Carbon $date): float
    {
        $reading = $this->dailyMeterReadings()
            ->where('obis_code', $obis)
            ->where('read_date', '<=', $date->format('Y-m-d'))
            ->orderBy('last_reading_at', 'DESC')
            ->first();

        return $reading?->closing ?? 0;
    }

    public function usageByObisAndDate(?string $obis, Carbon $start, Carbon $end): float
    {
        // Fix this shit.
        if ($obis === 'landscape_water' || $obis === 'coop_water') {
            $obis = '8.1.1.0.0.255';
        }

        return filled($obis)
            ? $this->lastReadingByObisAndDate($obis, $end->endOfDay()) - $this->firstReadingByObisAndDate($obis, $start->startOfDay())
            : 0;
    }

    public function spendThisMonth()
    {
        return $this->invoiceLineItemsThisMonth?->sum('amount') ?? 0;
    }

    public function usageThisMonth()
    {
        return $this->invoiceLineItemsThisMonth?->filter(fn ($item) => $item->base_charge === false)?->sum('quantity') ?? 0;
    }

    public function spendLastMonth()
    {
        return $this->invoiceLineItemsLastMonth?->sum('amount') ?? 0;
    }

    public function usageLastMonth()
    {
        return $this->invoiceLineItemsLastMonth?->filter(fn ($item) => $item->base_charge === false)?->sum('quantity') ?? 0;
    }

    public function lineItemsThisMonth()
    {
        if ($this->service) {
            return $this->service->recurringProducts()
                ->map(
                    function (Product $product) {
                        return (new MeterSpendCalculator(
                            $this,
                            $product,
                            $this->usageByObisAndDate($product->obisCode?->code, now()->startOfMonth(), now())
                        ))->get();
                    }
                )->flatten(1);
        }

        return collect();
    }

    public function lineItemsLastMonth(): Collection
    {
        if ($this->service) {
            return $this->service->recurringProducts()
                ->map(fn (Product $product) => (new MeterSpendCalculator(
                    $this,
                    $product,
                    $this->usageByObisAndDate($product->obisCode?->code, now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth())
                ))->get()
                )->flatten(1);
        }

        return collect();
    }

    public function lineItemsByDate(Carbon $start, Carbon $end): Collection
    {
        $this->loadMissing(
            'service.products.prices',
            'service.products.price',
            'service.products.obisCode',
        );
        if ($this->service) {
            return $this->service->recurringProducts()
                ->map(fn (Product $product) => (new MeterSpendCalculator(
                    $this,
                    $product,
                    $this->usageByObisAndDate($product->obisCode?->code, $start, $end)
                ))->get()
                )->flatten(1);
        }

        return collect();
    }
}
