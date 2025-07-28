<?php

namespace App\Http\Filters;

use Billow\Utilities\QueryFilter;

class PropertyFilters extends QueryFilter
{
    public function search($search)
    {
        return $this->builder
            ->when(
                filled($search),
                fn ($query) => $query->where(
                    fn ($query) => $query->orWhere('name', 'ILIKE', "{$search}%")
                        ->orWhereHas('customer', fn ($query) => $query->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('company_name', 'ILIKE', "%{$search}%"))
                )
            );
    }
}
