<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterSummary extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'meter_id',
        'unit',
        'usage',
        'cumulative_usage',
        'cost',
        'month',
    ];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function cost(): Attribute
    {
        return new Attribute(
            get: fn ($amount) => $amount / 100,
            set: fn ($amount) => round($amount, 2) * 100
        );
    }
}
