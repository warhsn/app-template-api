<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Allocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'allocatable_id',
        'allocatable_type',
        'payment_id',
    ];

    public function allocatable(): MorphTo
    {
        return $this->morphTo('allocatable');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function amount(): Attribute
    {
        return new Attribute(
            get: fn ($amount) => $amount / 100,
            set: fn ($amount) => $amount * 100
        );
    }
}
