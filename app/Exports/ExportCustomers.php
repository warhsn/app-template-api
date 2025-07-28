<?php

namespace App\Exports;

use App\Http\Filters\CustomerFilters;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportCustomers implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly CustomerFilters $filters,
    ) {}

    public function collection(): Collection
    {
        return Customer::query()
            ->filter($this->filters)
            ->withCount('meters', 'properties', 'primaryUser')
            ->get()
            ->sortBy('display_name');
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Primary User',
            'Entity',
            'Meters',
            'Properties',
            // 'Usage This Month',
            // 'Usage Last Month',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->display_name,
            $customer->primaryUser?->name,
            $customer->company_name,
            $customer->meters_count,
            $customer->properties_count,
            // $customer->spendLastMonth(),
            // $customer->spendThisMonth(),
        ];
    }
}
