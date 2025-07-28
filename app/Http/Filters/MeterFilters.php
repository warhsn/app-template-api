<?php

namespace App\Http\Filters;

use Billow\Utilities\QueryFilter;

class MeterFilters extends QueryFilter
{
    public function search($search)
    {
        return $this->builder
            ->when(
                filled($search), fn ($query) => $query->where('serial_number', 'ILIKE', "%{$search}%")
                    ->orWhereHas('property', fn ($query) => $query->where('name', 'ILIKE', "%{$search}%"))
                    ->orWhereHas('customer', fn ($query) => $query->where('first_name', 'ILIKE', "%{$search}%")
                        ->orWhere('last_name', 'ILIKE', "%{$search}%")
                        ->orWhere('company_name', 'ILIKE', "%{$search}%"))
            );
    }

    public function customer($customer)
    {
        return $this->builder
            ->when(
                filled($customer), fn ($query) => $query->where('customer_id', $customer)
            );
    }

    public function property_id($property)
    {
        return $this->builder
            ->when(
                filled($property), fn ($query) => $query->where('property_id', $property)
            );
    }

    public function meter_type_id($type)
    {
        return $this->builder
            ->when(
                filled($type), fn ($query) => $query->where('meter_type_id', $type)
            );
    }

    public function unallocated($unallocated)
    {
        return $this->builder
            ->when(
                filled($unallocated) && $unallocated === true, fn ($query) => $query->whereNull('customer_id')
            );
    }

    public function internal($internal)
    {
        return $this->builder
            ->when(
                filled($internal) && $internal === true, fn ($query) => $query->where('is_internal', true)
            );
    }

    public function customer_id($customerId)
    {
        return $this->builder
            ->when(
                filled($customerId), fn ($query) => $query->where('customer_id', $customerId)
            );
    }
}
