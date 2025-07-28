<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;

class LoadAllCustomersUserList extends Controller
{
    public function __invoke()
    {
        return User::orderBy('name')
            ->select('id as value', 'name as label', 'email', 'phone_number')
            ->get();
    }
}
