<?php

namespace App\Exports;

use App\Http\Filters\MeterFilters;
use App\Models\Meter;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportMeters implements FromQuery, WithChunkReading, WithHeadings, WithMapping
{
    public function __construct(
        private readonly MeterFilters $filters,
    ) {}

    public function query()
    {
        $meters = auth()->user()->role !== 'customer' ? Meter::query() : auth()->user()->meters();

        return $meters->filter($this->filters)
            ->orderBy('serial_number')
            ->select([
                'meters.id',
                'meters.customer_id',
                'meters.is_internal',
                'meters.key',
                'serial_number',
                'lat',
                'lng',
                'meters.notes',
                'meter_type_id',
                'meters.property_id',
                'service_id',
            ])
            ->with([
                'type:id,name',
                'property:id,name,original_portion_number',
                'service:id,name',
                'currentReading:meter_id,read_at,user_id',
                'customer:id,first_name,last_name,company_name',
            ]);
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function headings(): array
    {
        return [
            'Serial Number',
            'Type',
            'Customer',
            'Property',
            'Orginal Portion #',
            'Service',
            'Latitude',
            'Longitude',
            'Opening Reading',
            'Closing Reading',
            'Opening Reading Date',
            'Closing Reading Date',
            'Total Usage',
            'Notes',
            'Latest Reading',
            'Read By',
            'Offline',
            'Internal',
            'Farmsync ID',
        ];
    }

    public function map($meter): array
    {
        $opening = $meter->dailyMeterReadings()->where('read_date', '<=', today()->subMonth()->startOfMonth())->orderBy('read_date', 'desc')->first();

        $closing = $meter->dailyMeterReadings()->where('read_date', '>=', today()->subMonth()->endOfMonth())->orderBy('read_date', 'asc')->first();

        return [
            $meter->serial_number,
            $meter->type?->name,
            $meter->customer?->display_name,
            $meter->property?->name,
            $meter->property?->original_portion_number,
            $meter->service?->name,
            $meter->lat,
            $meter->lng,
            $opening?->opening,
            $closing?->closing,
            $opening?->read_date,
            $closing?->read_date,
            $closing?->opening - $opening?->closing,
            $meter->notes,
            $meter->currentReading?->read_at,
            $meter->currentReading?->user_id,
            $this->getStatus($meter->currentReading),
            $meter->is_internal ? 'Internal' : 'Customer',
            $meter->key,
        ];
    }

    private function getStatus($reading)
    {
        if (! $reading) {
            return 'Offline';
        }

        return Carbon::parse($reading?->read_at)->isBefore(
            today()->subDay()
        ) ? 'Offline' : 'Online';
    }
}
