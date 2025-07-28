<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InvoiceLineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'vat',
        'discount',
        'quantity',
        'total',
        'invoice_id',
        'product_id',
        'price_id',
        'invoiceable_id',
        'invoiceable_type',
        'service_id',
        'date',
        'base_charge',
        'original_price',
    ];

    public function invoiceable(): MorphTo
    {
        return $this->morphTo('invoiceable');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    protected function amount(): Attribute
    {
        return new Attribute(
            get: fn ($amount) => $amount / 100,
            set: fn ($amount) => $amount * 100
        );
    }

    protected function originalPrice(): Attribute
    {
        return new Attribute(
            get: fn ($amount) => $amount / 100,
            set: fn ($amount) => $amount * 100
        );
    }

    protected function vat(): Attribute
    {
        return new Attribute(
            get: fn ($vat) => $vat / 100,
            set: fn ($vat) => $vat * 100
        );
    }

    protected function discount(): Attribute
    {
        return new Attribute(
            get: fn ($discount) => $discount / 100,
            set: fn ($discount) => $discount * 100
        );
    }

    protected function total(): Attribute
    {
        return new Attribute(
            get: fn ($total) => $total / 100,
            set: fn ($total) => $total * 100
        );
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'base_charge' => 'boolean',
            'quantity' => 'float',
        ];
    }
}
