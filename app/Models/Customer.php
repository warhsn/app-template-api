<?php

namespace App\Models;

use Billow\Utilities\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Customer extends Model
{
    use Filterable, HasFactory, HasRelationships, Searchable;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'registration_number',
        'vat_number',
        'user_id',
        'billing_email',
        'billing_entity',
        'billing_address',
        'current_address',
        'phone_number',
        'primary_user_id',
        'street',
        'suburb',
        'city',
        'postal_code',
        'province',
        'billing_address_property_id',
        'notes',
    ];

    protected $appends = ['full_name', 'display_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function billingAddress(): HasOne
    {
        return $this->hasOne(Property::class, 'id', 'billing_address_property_id');
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function primaryUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasManyDeep(
            User::class,
            [Property::class, 'property_user'],
            [
                'customer_id',
                'property_id',
                'id',
            ],
            [
                'id',
                'id',
                'user_id',
            ]
        );
    }

    public function services(): HasManyThrough
    {
        return $this->hasManyThrough(
            Service::class,
            Meter::class,
            'customer_id',
            'id',
            'id',
            'service_id'
        );
    }

    public function fullName(): Attribute
    {
        return new Attribute(
            get: fn () => "{$this->first_name} {$this->last_name}"
        );
    }

    public function displayName(): Attribute
    {
        return new Attribute(
            get: fn () => filled($this->company_name) ? $this->company_name." {$this->first_name} {$this->last_name}" : "{$this->first_name} {$this->last_name}"
        );
    }

    public function outstanding(): float
    {
        $totalPayments = $this->payments->sum('amount');
        $totalInvoices = $this->invoices->sum('total');

        return $totalInvoices - $totalPayments;
    }

    public function overdueAmount(): float
    {
        return $this->invoices()
            ->unpaid()
            ->overdue()
            ->get()
            ->reduce(fn ($total, $invoice) => $total + $invoice->outstanding(), 0);
    }

    public function spendThisMonth()
    {
        $this->loadMissing('properties.meters.service.products.prices');

        return Cache::remember("{$this->id}-customer-spend-this-month", 5000, function () {
            return $this->properties->reduce(fn ($total, $property) => $total + $property->spendThisMonth(), 0);
        });
    }

    public function spendLastMonth()
    {
        $this->loadMissing('properties.meters.service.products.prices');

        return Cache::remember("{$this->id}-customer-spend-last-month", 5000, function () {
            return $this->properties->reduce(fn ($total, $property) => $total + $property->spendLastMonth(), 0);
        });
    }

    public function spendByDate(Carbon $startDate, Carbon $endDate)
    {
        $this->loadMissing('properties.meters.service.products.prices');

        return $this->properties->reduce(fn ($total, $property) => $total + $property->spendByDate($startDate, $endDate), 0);
    }
}
