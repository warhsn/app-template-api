<?php

namespace App\Http\Filters;

use Billow\Utilities\QueryFilter;

class InvoiceFilters extends QueryFilter
{
    public function search($search)
    {
        return $this->builder
            ->when(
                filled($search),
                fn ($query) => $query->where(
                    fn ($query) => $query->orWhere('number', 'ILIKE', "%{$search}%")
                        ->orWhereHas('customer', fn ($query) => $query->where('customers.first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('customers.company_name', 'ILIKE', "%{$search}%")
                            ->orWhere('customers.last_name', 'ILIKE', "%{$search}%")
                        )
                )
            );
    }
}
