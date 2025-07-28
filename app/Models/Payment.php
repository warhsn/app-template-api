<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'customer_id',
        'date',
        'reference',
    ];

    protected $dates = [
        'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function amount(): Attribute
    {
        return new Attribute(
            get: fn ($amount) => $amount / 100,
            set: fn ($amount) => $amount * 100
        );
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function allocatable(): float
    {
        $this->loadMissing('allocations');

        return $this->amount - $this->allocations->sum('amount');
    }
}
