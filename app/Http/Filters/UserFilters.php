<?php

namespace App\Http\Filters;

use Billow\Utilities\QueryFilter;

class UserFilters extends QueryFilter
{
    public function search($search)
    {
        return $this->builder
            ->when(
                filled($search),
                fn ($query) => $query->where(
                    fn ($query) => $query->orWhere('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                )
            );
    }
}
