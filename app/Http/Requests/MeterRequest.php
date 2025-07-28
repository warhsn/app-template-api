<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $serialNumberRules = ['required'];

        $this->routeIs('meters.create')
            ? $serialNumberRules[] = 'unique:meters,serial_number'
            : Rule::unique('meters', 'serial_number')->ignore($this->route('meter'));

        return [
            'serial_number' => $serialNumberRules,
            'meter_type_id' => 'required|exists:meter_types,id',
            'service_id' => 'required|exists:services,id',
            'lat' => 'nullable|numeric|between:-90,90|regex:/^-?\d{1,2}(\.\d{1,15})?$/',
            'lng' => 'nullable|numeric|between:-180,180|regex:/^-?\d{1,3}(\.\d{1,15})?$/',
        ];
    }
}
