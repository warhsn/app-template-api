<?php

namespace App\Exports;

use App\Models\Meter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportMeterDailyReadings implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private Collection $readings,
    ) {}

    public function collection(): Collection
    {
        return $this->readings;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Opening',
            'Closing',
            'Total',
            'Type',
        ];
    }

    public function map($reading): array
    {
        return [
            $reading->read_date,
            (float) $reading->opening,
            (float) $reading->closing,
            round((float) $reading->closing - (float) $reading->opening, 3),
            $this->getObisCode($reading->obis_code),
        ];
    }

    private function getObisCode($code)
    {
        return match ($code) {
            Meter::CUMULATIVE_ELECTRICAL_FEEDBACK_OBIS => 'Feedback',
            default => 'Usage'
        };
    }
}
