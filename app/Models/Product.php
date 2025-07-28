<?php

namespace App\Models;

use Billow\Utilities\Traits\Filterable;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class Product extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'product_type_id',
        'tiered',
        'recurring',
        'priceable_id',
        'priceable_type',
        'obis_code_id',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function obisCode(): BelongsTo
    {
        return $this->belongsTo(ObisCode::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class)->orderBy('amount', 'asc');
    }

    public function price(): HasOne
    {
        return $this->hasOne(Price::class)->latest();
    }

    public function priceable(): MorphTo
    {
        return $this->morphTo('priceable');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function scopeTiered($query): Builder
    {
        return $query->whereTiered(true);
    }

    public function scopeNotTiered($query): Builder
    {
        return $query->whereTiered(false);
    }

    public function scopeRecurring($query): Builder
    {
        return $query->whereRecurring(true);
    }

    public function displayPrice(): string
    {
        $prices = $this->prices->sortBy('tier_limit');
        $formatter = new NumberFormatter('en_ZA', NumberFormatter::CURRENCY);
        if ($prices->count() === 1) {
            return $formatter->formatCurrency($prices->first()->amount, 'ZAR');
        }
        $startingPrice = $formatter->formatCurrency($prices->first()->amount, 'ZAR');
        $topPrice = $formatter->formatCurrency($prices->last()->amount, 'ZAR');

        return "{$startingPrice} - {$topPrice}";
    }
}
