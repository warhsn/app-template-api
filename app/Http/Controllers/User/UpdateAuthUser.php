<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Storage;

class UpdateAuthUser extends Controller
{
    public function __invoke(UserRequest $request)
    {
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone_number' => $request->phone_number,
        ];

        if ($request->hasFile('profile_picture')) {
            if (auth()->user()->profile_photo_path) {
                Storage::disk('local')->delete(auth()->user()->profile_photo_path);
            }
            $path = $request->file('profile_picture')->store('profile-photos', 'public');
            $updateData['profile_photo_path'] = $path;
        }

        auth()->user()->update($updateData);

        return auth()->user();
    }
}
