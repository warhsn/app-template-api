<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\WelcomeInternalUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser extends Controller
{
    public function __invoke(UserRequest $request)
    {
        $password = Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => trim(strtolower($request->email)),
            'role' => $request->role,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($password),
        ]);

        if ($request->role === 'customer') {
            $user->properties()->attach($request->propertyIds);
        }

        $user->notify(new WelcomeInternalUser($user, $password));

        return $user;
    }
}
