<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Meter;
use Illuminate\Support\Facades\Cache;

class LoadAdminDashboard extends Controller
{
    public function __invoke()
    {
        return [
            'offline_meters_count' => $this->getOfflineMetersCount(),
            'online_meters_count' => $this->getOnlineMetersCount(),
            'electrical_usage_this_month' => Cache::remember('admin_electrical_usage_this_month', 10000, fn () => $this->getElectricalUsageThisMonth()),
            'electrical_usage_last_month' => Cache::remember('admin_electrical_usage_last_month', 10000, fn () => $this->getElectricalUsageLastMonth()),
            'water_usage_this_month' => Cache::remember('admin_water_usage_this_month', 10000, fn () => $this->getWaterUsageThisMonth()),
            'water_usage_last_month' => Cache::remember('admin_water_usage_last_month', 10000, fn () => $this->getWaterUsageLastMonth()),
            'landscape_water_usage_this_month' => Cache::remember('admin_landscape_water_usage_this_month', 10000, fn () => $this->getLandscapeWaterUsageThisMonth()),
            'landscape_water_usage_last_month' => Cache::remember('admin_landscape_water_usage_last_month', 10000, fn () => $this->getLandscapeWaterUsageLastMonth()),
            'coop_water_usage_this_month' => Cache::remember('admin_coop_water_usage_this_month', 10000, fn () => $this->getCOOPWaterUsageThisMonth()),
            'coop_water_usage_last_month' => Cache::remember('admin_coop_water_usage_last_month', 10000, fn () => $this->getCOOPWaterUsageLastMonth()),
            'water_usage_last_month' => Cache::remember('admin_water_usage_last_month', 10000, fn () => $this->getWaterUsageLastMonth()),
            'meter_markers' => $this->getMeterMarkers(),
            'anomolous_meters' => $this->getAnomolousMeters(),
        ];
    }

    private function getOfflineMetersCount()
    {
        return Meter::whereNull('decommissioned_at')
            ->whereNotNull('property_id')
            ->where('is_internal', false)
            ->whereDoesntHave(
                'readings',
                fn ($query) => $query->where('read_at', '>=', today()->subHours(24))
            )->whereHas('type', fn ($query) => $query->whereIn('name', [
                'Domestic Water',
                'Three Phase Electricity',
                'Single Phase Electricity',
            ]))->with('type', 'property', 'customer')
            ->count();
    }

    private function getOnlineMetersCount()
    {
        return Meter::whereHas(
            'readings',
            fn ($query) => $query->where('read_at', '>=', today()->subHours(24))
        )->with('type', 'property', 'customer')
            ->count();
    }

    private function getMeterMarkers()
    {
        return Meter::whereNotNull('lat')
            ->with('type')
            ->get();
    }

    private function getElectricalUsageThisMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsThisMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->unit === 'kWh')
            ->map(fn ($meter) => $meter->invoiceLineItemsThisMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getElectricalUsageLastMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsLastMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->unit === 'kWh')
            ->map(fn ($meter) => $meter->invoiceLineItemsLastMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getWaterUsageThisMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsThisMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->name === 'Domestic Water')
            ->map(fn ($meter) => $meter->invoiceLineItemsThisMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getWaterUsageLastMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsLastMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->name === 'Domestic Water')
            ->map(fn ($meter) => $meter->invoiceLineItemsLastMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getLandscapeWaterUsageThisMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsThisMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->name === 'Landscape Irrigation Water')
            ->map(fn ($meter) => $meter->invoiceLineItemsThisMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getLandscapeWaterUsageLastMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsLastMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->name === 'Landscape Irrigation Water')
            ->map(fn ($meter) => $meter->invoiceLineItemsLastMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getCOOPWaterUsageThisMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsThisMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->name === 'COOP Irrigation Water')
            ->map(fn ($meter) => $meter->invoiceLineItemsThisMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getCOOPWaterUsageLastMonth()
    {
        return Meter::query()
            ->with('invoiceLineItemsLastMonth', 'type')
            ->get()
            ->filter(fn ($meter) => $meter->type->name === 'COOP Irrigation Water')
            ->map(fn ($meter) => $meter->invoiceLineItemsLastMonth->filter(fn ($lineItem) => ! $lineItem->base_charge))
            ->flatten()
            ->sum('quantity');
    }

    private function getAnomolousMeters()
    {
        $query = auth()->user()->role === 'customer' ? auth()->user()->meters() : Meter::query();

        return $query->where('has_anomaly', true)
            ->with('anomaly', 'property', 'customer')
            ->get();
    }
}
