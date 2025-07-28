<?php

namespace App\Http\Filters;

use Billow\Utilities\QueryFilter;

class CustomerFilters extends QueryFilter
{
    public function search($search)
    {
        return $this->builder
            ->when(filled($search), fn ($query) => $query->where(
                fn ($query) => $query->orWhere(
                    'first_name',
                    'ILIKE',
                    "%{$search}%"
                )
                    ->orWhere('last_name', 'ILIKE', "%{$search}%")
                    ->orWhere('company_name', 'ILIKE', "%{$search}%")
                    ->orWhereHas('users', fn ($query) => $query->where('users.name', 'ilike', "%{$search}%"))
            ));
    }
}
