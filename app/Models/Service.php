<?php

namespace App\Models;

use App\Services\MeterSpendCalculator;
use Billow\Utilities\Traits\Filterable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection as SupportCollection;

class Service extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function recurringProducts(): Collection
    {
        $this->loadMissing('products.prices', 'products.price', 'products.obisCode');

        return $this->products()
            ->recurring()
            ->get();
    }

    public function usageThisMonthByMeters(SupportCollection $meters)
    {
        return $meters->map(fn ($meter) => $this->recurringProducts()
            ->map(
                fn (Product $product) => (new MeterSpendCalculator(
                    $meter,
                    $product,
                    $meter->usageThisMonthByObis($product->obisCode?->code)
                ))->get()
            )->flatten(1))
            ->flatten(1);
    }
}
