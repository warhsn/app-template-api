<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatedPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Controller
{
    public function __invoke(User $user, UpdatedPassword $request)
    {
        $user->update([
            'password' => Hash::make($request->password),
        ]);
    }
}
