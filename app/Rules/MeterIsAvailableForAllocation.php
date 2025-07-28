<?php

namespace App\Rules;

use App\Models\Meter;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MeterIsAvailableForAllocation implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (filled($value)) {
            $meter = Meter::find($value);
            if (filled($meter->property_id)) {
                $fail('This meter is already allocated');
            }
        }
    }
}
