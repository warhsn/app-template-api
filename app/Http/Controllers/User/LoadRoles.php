<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class LoadRoles extends Controller
{
    public function __invoke()
    {
        return [
            ['value' => 'admin', 'label' => 'Admin'],
            ['value' => 'customer', 'label' => 'Customer'],
            ['value' => 'finance', 'label' => 'Finance'],
            ['value' => 'operations', 'label' => 'Operations'],
        ];
    }
}
