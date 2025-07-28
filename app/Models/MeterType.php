<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeterType extends Model
{
    use HasFactory;

    public const SINGLE_PHASE_SMART_METER = 'Single Phase Electricity';

    public const THREE_PHASE_SMART_METER = 'Three Phase Electricity';

    public const DOMESTIC_SMART_WATER_METER = 'Domestic Water';

    public const COOP_IRRIGATION_SMART_WATER_METER = 'COOP Irrigation Water';

    public const LANDSCAPE_IRRIGATION_SMART_WATER_METER = 'Landscape Irrigation Water';

    public const UNIDENTIFIED_METER = 'Unidentified Meter';

    protected $fillable = [
        'name',
        'unit',
    ];

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function prices()
    {
        return $this->morphMany(Price::class, 'priceable');
    }
}
