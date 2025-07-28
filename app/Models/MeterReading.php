<?php

namespace App\Models;

use Billow\Utilities\Traits\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class MeterReading extends Model implements AuditableContract
{
    use Auditable, Filterable, HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'value',
        'meter_id',
        'obis_code',
        'read_at',
        'unit',
        'photo',
        'user_id',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByObis($query, $obis)
    {
        return $query->whereObisCode($obis);
    }

    public function photoUrl(): Attribute
    {
        return new Attribute(
            get: fn () => $this->photo ? config('app.url').Storage::url($this->photo) : null
        );
    }

    // protected function casts(): array
    // {
    //     return [
    //         'read_at' => 'datetime'
    //     ];
    // }
}
