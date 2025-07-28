<?php

namespace App\Exports;

use App\Models\Meter;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportLandscapeMeters implements FromQuery, WithChunkReading, WithHeadings, WithMapping
{
    public function __construct(
        private readonly string $date,
    ) {}

    public function query()
    {
        return Meter::query()
            ->whereHas('type', fn ($query) => $query->where('name', 'Landscape Irrigation Water'))
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
            'Opening Reading Date',
            'Closing Reading',
            'Closing Reading Date',
            'Total Usage',
            'Notes',
            'Internal',
            'Farmsync ID',
        ];
    }

    public function map($meter): array
    {
        $opening = $meter->dailyMeterReadings()->where('read_date', '<=', now()->parse($this->date)->startOfMonth())->orderBy('read_date', 'desc')->first();
        $closing = $meter->dailyMeterReadings()->where('read_date', '<=', now()->parse($this->date)->endOfMonth())->orderBy('read_date', 'desc')->first();

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
            $closing?->closing - $opening?->opening,
            $meter->notes,
            $meter->is_internal ? 'Yes' : 'No',
            $meter->key,
        ];
    }
}
