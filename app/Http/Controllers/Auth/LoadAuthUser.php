<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class LoadAuthUser extends Controller
{
    public function __invoke()
    {
        return request()->user();
    }
}
