<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;

class Manager extends Controller
{
    public function __invoke(User $user)
    {
        return view('pages.user.manager', [
            'user' => $user,
        ]);
    }
}
