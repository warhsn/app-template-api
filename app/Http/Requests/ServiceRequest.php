<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nameRules = ['required', 'max:150'];

        $this->routeIs('services.create')
            ? $nameRules[] = 'unique:services,name'
            : $nameRules[] = Rule::unique('services', 'name')->ignore($this->route('service'));

        return [
            'name' => $nameRules,
            'product_ids' => 'array|sometimes',
        ];
    }
}
