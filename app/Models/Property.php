<?php

namespace App\Models;

use Billow\Utilities\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;

class Property extends Model
{
    use Filterable, HasFactory, Searchable;

    protected $fillable = [
        'name',
        'square_meters',
        'customer_id',
        'address',
        'water_rights',
        'sub_dividable',
        'portion_number',
        'original_portion_number',
        'landscape_size',
        'coop_size',
        'developer_id',
        'street',
        'suburb',
        'city',
        'postal_code',
        'province',
        'notes',
    ];

    protected $appends = ['hectares', 'landscape_hectares', 'coop_hectares'];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function spendThisMonth(): float
    {
        return Cache::remember("{$this->id}-property-spend-this-month", 3600, function () {
            $this->loadMissing('meters.currentMonthInvoice');

            return $this->meters->reduce(static fn ($total, $meter) => $total + $meter->spendThisMonth(), 0);
        });
    }

    public function spendLastMonth(): float
    {
        return Cache::remember("{$this->id}-property-spend-last-month", 3600, function () {
            $this->loadMissing(
                'meters.readings',
                'meters.service.products.prices',
                'meters.service.products.price',
                'meters.service.products.obisCode',
            );

            return $this->meters->reduce(static function ($total, $meter) {
                return $total + $meter->spendLastMonth();
            }, 0);
        });
    }

    public function spendByDate(Carbon $startDate, Carbon $endDate): float
    {
        return $this->meters->reduce(
            static fn ($total, $meter) => $total + $meter->lineItemsByDate($startDate, $endDate)->sum('amount'),
            0
        );
    }

    public function hectares(): Attribute
    {
        return new Attribute(
            get: fn () => $this->square_meters / 10000
        );
    }

    public function landscapeHectares(): Attribute
    {
        return new Attribute(
            get: fn () => $this->landscape_size / 10000
        );
    }

    public function coopHectares(): Attribute
    {
        return new Attribute(
            get: fn () => $this->coop_size / 10000
        );
    }
}
