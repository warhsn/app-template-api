<?php

namespace App\Models;

use Billow\Utilities\Traits\Filterable;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Invoice extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'number',
        'vat_rate',
        'vat',
        'amount',
        'discount',
        'total',
        'customer_id',
        'property_id',
        'due_at',
        'paid_at',
        'date',
        'is_final',
    ];

    protected $appends = [
        'display_number',
        'is_overdue',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class);
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(Allocation::class, 'allocatable');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereDate(now()->format('Y-m-d'));
    }

    public function scopePaid($query): Builder
    {
        return $query->whereNotNull('paid_at');
    }

    public function scopeUnpaid($query): Builder
    {
        return $query->whereNull('paid_at');
    }

    public function scopeOverdue($query): Builder
    {
        return $query->wherePast('due_at');
    }

    #[Scope]
    public function isFinal($query): void
    {
        $query->whereIsFinal(true);
    }

    public function outstanding(): float
    {
        $this->loadMissing('allocations');

        return $this->total - $this->allocations->sum('amount');
    }

    public function amount(): Attribute
    {
        return new Attribute(
            get: fn ($amount) => $amount / 100,
            set: fn ($amount) => $amount * 100
        );
    }

    public function vat(): Attribute
    {
        return new Attribute(
            get: fn ($vat) => $vat / 100,
            set: fn ($vat) => $vat * 100
        );
    }

    public function discount(): Attribute
    {
        return new Attribute(
            get: fn ($discount) => $discount / 100,
            set: fn ($discount) => $discount * 100
        );
    }

    public function total(): Attribute
    {
        return new Attribute(
            get: fn ($total) => $total / 100,
            set: fn ($total) => $total * 100
        );
    }

    public function displayNumber(): Attribute
    {
        return new Attribute(
            get: fn () => "IN{$this->number}"
        );
    }

    public function isOverdue(): Attribute
    {
        return new Attribute(
            get: fn () => $this->due_at->isPast() && ! $this->paid_at
        );
    }

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'paid_at' => 'datetime',
            'date' => 'date',
            'is_final' => 'boolean',
        ];
    }
}
