<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $emailRules = ['required', 'email'];

        $this->routeIs('user.create')
            ? $emailRules[] = ['unique:users,email']
            : $emailRules[] = Rule::unique('users', 'email')->ignore($this->route('user') ?? auth()->user());

        return [
            'name' => 'required',
            'email' => $emailRules,
            'role' => 'required',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ];
    }
}
