<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anomaly extends Model
{
    protected $fillable = [
        'value',
        'meter_id',
    ];

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
}
