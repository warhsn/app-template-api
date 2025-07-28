<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DailyMeterReadings extends Model
{
    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function openingPhotoUrl(): Attribute
    {
        return new Attribute(
            get: fn () => $this->opening_photo ? Storage::url($this->opening_photo) : null
        );
    }

    public function closingPhotoUrl(): Attribute
    {
        return new Attribute(
            get: fn () => $this->closing_photo ? Storage::url($this->closing_photo) : null
        );
    }
}
