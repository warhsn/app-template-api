<?php

namespace App\Http\Requests;

use App\Models\Meter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class NewMeterReading extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $meter = $this->route('meter');

        return [
            'value' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('meter_readings')
                        ->where('meter_id', $this->route('meter')->id)
                        ->where('read_at', $this->get('date'))
                        ->where('obis_code', $this->getObis())
                        ->whereRaw('ABS(value - ?) < 0.001', [$value])
                        ->exists();

                    if ($exists) {
                        $fail('A meter reading with this value already exists for the selected meter and date.');
                    }
                },
            ],
            'read_at' => 'required|date',
            'photo' => 'required|image',
        ];
    }

    private function getObis()
    {
        if (str_contains($this->route('meter')->type->name, 'Water')) {
            return Meter::CUMULATIVE_WATER_OBIS;
        }

        return Meter::CUMULATIVE_ELECTRICAL_OBIS;
    }
}
