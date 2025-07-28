<?php

namespace App\Http\Filters;

use Billow\Utilities\QueryFilter;

class ProductFilters extends QueryFilter
{
    public function search($search)
    {
        return $this->builder
            ->when(
                filled($search),
                fn ($query) => $query->where('name', 'ilike', "%{$search}%")
            );
    }
}
