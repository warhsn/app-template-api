<?php

namespace App\Exports;

use App\Models\Meter;
use App\Models\ObisCode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MeterReadingsExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Meter $meter,
        private string $startDate,
        private string $endDate,
    ) {}

    public function collection()
    {
        return $this->meter->readings()
            ->byObis(ObisCode::whereName($this->meter->type->name)->first()->code)
            ->whereBetween('read_at', [$this->startDate, $this->endDate])
            ->latest('read_at')
            ->select('value', 'read_at', 'unit')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Value',
            'Read At',
            'Unit',
        ];
    }
}
