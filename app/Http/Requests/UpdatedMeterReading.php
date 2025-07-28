<?php

namespace App\Http\Requests;

use App\Models\Meter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdatedMeterReading extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $meter = $this->route('meter');
        $meterReading = $this->route('meterReading');

        return [
            'value' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($meter, $meterReading) {
                    $exists = DB::table('meter_readings')
                        ->where('meter_id', $meter->id)
                        ->where('read_at', $this->get('read_at'))
                        ->where('obis_code', $this->getObis($meter))
                        ->where('id', '!=', $meterReading->id) // Exclude current reading
                        ->whereRaw('ABS(value - ?) < 0.001', [$value])
                        ->exists();

                    if ($exists) {
                        $fail('A meter reading with this value already exists for the selected meter and date.');
                    }
                },
            ],
            'read_at' => 'required|date',
            'file' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB max
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'read_at' => 'reading date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'value.required' => 'The meter reading value is required.',
            'value.numeric' => 'The meter reading value must be a number.',
            'read_at.required' => 'The reading date is required.',
            'read_at.date' => 'The reading date must be a valid date.',
            'file.file' => 'The photo must be a valid file.',
            'file.mimes' => 'The photo must be a file of type: jpeg, jpg, png, gif, or webp.',
            'file.max' => 'The photo must not be larger than 10MB.',
        ];
    }

    private function getObis(Meter $meter)
    {
        if (str_contains($meter->type->name, 'Water')) {
            return Meter::CUMULATIVE_WATER_OBIS;
        }

        return Meter::CUMULATIVE_ELECTRICAL_OBIS;
    }
}
