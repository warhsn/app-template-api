<?php

namespace App\Exports;

use App\Models\Meter;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportSinglePhaseMeters implements FromQuery, WithChunkReading, WithHeadings, WithMapping
{
    public function __construct(
        private readonly string $date,
    ) {}

    public function query()
    {
        return Meter::query()
            ->notInternal()
            ->whereHas('type', fn ($query) => $query->where('name', 'Single Phase Electricity'))
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
                'invoices' => fn ($query) => $query->whereDate('invoices.date', $this->date)->with('lineItems'),
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
            'Opening Usage Reading',
            'Closing Usage Reading',
            'Opening Feedback Reading',
            'Closing Feedback Reading',
            'Total Usage',
            'Total Ex Vat',
            'Notes',
            'Internal',
            'Farmsync ID',
        ];
    }

    public function map($meter): array
    {
        $openingUsage = $meter->dailyMeterReadings()
            ->where('obis_code', Meter::CUMULATIVE_ELECTRICAL_OBIS)
            ->where('read_date', '<=', now()->parse($this->date)->startOfMonth())
            ->orderBy('read_date', 'desc')
            ->first();
        $closingUsage = $meter->dailyMeterReadings()
            ->where('obis_code', Meter::CUMULATIVE_ELECTRICAL_OBIS)
            ->where('read_date', '<=', now()->parse($this->date)->endOfMonth())
            ->orderBy('read_date', 'desc')
            ->first();

        $openingFeedback = $meter->dailyMeterReadings()
            ->where('obis_code', Meter::CUMULATIVE_ELECTRICAL_FEEDBACK_OBIS)
            ->where('read_date', '<=', now()->parse($this->date)->startOfMonth())
            ->orderBy('read_date', 'desc')
            ->first();
        $closingFeedback = $meter->dailyMeterReadings()
            ->where('obis_code', Meter::CUMULATIVE_ELECTRICAL_FEEDBACK_OBIS)
            ->where('read_date', '<=', now()->parse($this->date)->endOfMonth())
            ->orderBy('read_date', 'desc')
            ->first();

        $usage = ($closingUsage?->closing - $openingUsage?->opening) - ($closingFeedback?->closing - $openingFeedback?->opening);

        return [
            $meter->serial_number,
            $meter->type?->name,
            $meter->customer?->display_name,
            $meter->property?->name,
            $meter->property?->original_portion_number,
            $meter->service?->name,
            $meter->lat,
            $meter->lng,
            $openingUsage?->opening,
            $closingUsage?->closing,
            $openingFeedback?->opening,
            $closingFeedback?->closing,
            round($usage, 3),
            $this->getInvoiceAmount($meter),
            $meter->notes,
            $meter->is_internal ? 'Yes' : 'No',
            $meter->key,
        ];
    }

    private function getInvoiceAmount(Meter $meter)
    {
        $invoice = $meter->invoices->first();

        if ($invoice) {
            return $invoice->lineItems->where('invoiceable_id', $meter->id)
                ->sum('amount');
        }

        return 0;
    }
}
