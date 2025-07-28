<?php

namespace App\Http\Requests;

use App\Rules\MeterIsAvailableForAllocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nameRules = ['required', 'max:100'];

        $this->routeIs('properties.create')
            ? $nameRules[] = 'unique:properties,name'
            : $nameRules[] = Rule::unique('properties', 'name')->ignore($this->route('property'));

        return [
            'name' => $nameRules,
            'developer_id' => 'required|exists:developers,id',
            'domestic_water_meter_id' => ['sometimes', new MeterIsAvailableForAllocation],
            'electrical_water_meter_id' => ['sometimes', new MeterIsAvailableForAllocation],
            'landscape_water_meter_id' => ['sometimes', new MeterIsAvailableForAllocation],
            'coop_water_meter_id' => ['sometimes', new MeterIsAvailableForAllocation],
        ];
    }
}
