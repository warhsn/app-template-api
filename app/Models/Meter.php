<?php

namespace App\Models;

use App\Models\Traits\CalculatesMeters;
use Billow\Utilities\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Meter extends Model
{
    use CalculatesMeters, Filterable, HasFactory, HasRelationships, Searchable;

    public const CUMULATIVE_ELECTRICAL_OBIS = '1.1.1.8.0.255';

    public const CUMULATIVE_ELECTRICAL_FEEDBACK_OBIS = '1.1.2.8.0.255';

    public const CUMULATIVE_WATER_OBIS = '8.1.1.0.0.255';

    protected $fillable = [
        'serial_number',
        'meter_type_id',
        'customer_id',
        'property_id',
        'service_id',
        'lat',
        'lng',
        'manual_capture',
        'has_anomaly',
        'decommissioned_at',
        'radio_id',
        'notes',
        'is_internal',
        'key',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(MeterType::class, 'meter_type_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(MeterReading::class, 'meter_id');
    }

    public function currentReading(): HasOne
    {
        return $this->hasOne(MeterReading::class, 'meter_id')
            ->orderBy('read_at', 'desc')
            ->limit(1);
    }

    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    public function anomaly(): HasOne
    {
        return $this->hasOne(Anomaly::class)->latest();
    }

    public function radio(): BelongsTo
    {
        return $this->belongsTo(Radio::class);
    }

    public function usageSummaries(): HasMany
    {
        return $this->hasMany(MeterSummary::class, 'meter_id');
    }

    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            InvoiceLineItem::class,
            'invoiceable_id',
            'id',
            'id',
            'invoice_id'
        )->where('invoice_line_items.invoiceable_type', 'meter')
            ->distinct();
    }

    public function invoiceLineItems(): MorphMany
    {
        return $this->morphMany(InvoiceLineItem::class, 'invoiceable');
    }

    public function invoiceLineItemsThisMonth(): MorphMany
    {
        return $this->invoiceLineItems()
            ->whereDate('date', now()->endOfMonth()->format('Y-m-d'));
    }

    public function invoiceLineItemsLastMonth(): MorphMany
    {
        return $this->invoiceLineItems()
            ->whereDate('date', now()->subMonth()->endOfMonth()->format('Y-m-d'));
    }

    public function currentMonthInvoice()
    {
        return $this->invoices()
            ->where('invoices.date', Carbon::now()->endOfMonth()->format('Y-m-d'))
            ->latest('invoices.date');
    }

    public function lastMonthInvoice()
    {
        return $this->invoices()
            ->where('invoices.date', Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d'))
            ->latest('invoices.date');
    }

    public function scopeSinglePhaseElectricity($query): Builder
    {
        return $query->whereHas('type', fn ($query) => $query->whereName('Single Phase Electricity'));
    }

    public function scopeThreePhaseElectricity($query): Builder
    {
        return $query->whereHas('type', fn ($query) => $query->whereName('Three Phase Electricity'));
    }

    public function scopeDomesticWater($query): Builder
    {
        return $query->whereHas('type', fn ($query) => $query->whereName('Domestic Water'));
    }

    public function scopeIrrigationWater($query): Builder
    {
        return $query->whereHas('type', fn ($query) => $query->whereName('Irrigation Water'));
    }

    public function dailyMeterReadings(): HasMany
    {
        return $this->hasMany(DailyMeterReadings::class);
    }

    #[Scope]
    public function notInternal($query)
    {
        return $query->where('is_internal', false);
    }

    protected function casts(): array
    {
        return [
            'has_anomaly' => 'boolean',
            'is_internal' => 'boolean',
            'decommissioned_at' => 'datetime',
        ];
    }
}
