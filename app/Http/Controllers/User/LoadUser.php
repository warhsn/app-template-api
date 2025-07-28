<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;

class LoadUser extends Controller
{
    public function __invoke(User $user)
    {
        return $user->load('properties');
    }
}
