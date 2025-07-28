<?php

namespace App\Exports;

use App\Http\Filters\PropertyFilters;
use App\Models\Property;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportProperties implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private PropertyFilters $filters) {}

    public function collection(): Collection
    {
        $query = auth()->user()->role === 'customer' ? auth()->user()->properties() : Property::query();

        return $query->filter($this->filters)
            ->withCount('meters')
            ->with('customer', 'developer')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Address',
            'Portion #',
            'Orginal Portion #',
            'Subdividable',
            'COOP Irrigation Hectares',
            'Landscape Irrigation Hectares',
            'Customer',
            'Customer Entity',
            'Meters',
            'Hectares',
            'Developer',
            'Notes',
        ];
    }

    public function map($property): array
    {
        return [
            $property->id,
            $property->name,
            collect([$property->street, $property->suburb, $property->city, $property->postal_code, $property->province])
                ->filter()
                ->implode(', '),
            $property?->portion_number,
            $property?->original_portion_number,
            $property->sub_dividable,
            $property->coop_hectares,
            $property->landscape_hectares,
            $property->customer?->full_name,
            $property->customer?->company_name,
            $property->meters_count ?? '0',
            $property->hectares ?? '0.00',
            $property->developer?->name,
            $property->notes,
        ];
    }
}
