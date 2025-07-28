<?php

namespace App\Http\Controllers\User;

use App\Exports\ExportUsers;
use App\Http\Controllers\Controller;
use App\Http\Filters\UserFilters;
use Maatwebsite\Excel\Facades\Excel;

class ExportUserXlsx extends Controller
{
    public function __invoke(UserFilters $filters)
    {
        return Excel::download(
            new ExportUsers($filters),
            'properties.xlsx'
        );
    }
}
