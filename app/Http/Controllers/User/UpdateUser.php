<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;

class UpdateUser extends Controller
{
    public function __invoke(User $user, UserRequest $request)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->role === 'customer') {
            $user->properties()->sync($request->propertyIds);
        }

        return $user;
    }
}
