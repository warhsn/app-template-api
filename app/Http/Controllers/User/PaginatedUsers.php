<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Filters\UserFilters;
use App\Models\User;

class PaginatedUsers extends Controller
{
    public function __invoke(UserFilters $filters)
    {
        return User::orderBy('name')
            ->filter($filters)
            ->withCount('properties')
            ->paginate()
            ->setPath('users');
    }
}
