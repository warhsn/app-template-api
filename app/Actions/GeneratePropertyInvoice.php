<?php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Meter;
use App\Models\Price;
use App\Models\Property;

class GeneratePropertyInvoice
{
    public Invoice $invoice;

    public Property $property;

    public bool $lastMonth = false;

    public function handle(Property $property, bool $lastMonth = false)
    {
        $this->property = $property;
        $this->lastMonth = $lastMonth;

        if ($this->propertyHasSpend()) {
            $this->invoice();
            $this->invoice->lineItems()->delete();

            $property->meters->each(function ($meter) {
                $lineItems = $this->lastMonth ? $meter->lineItemsLastMonth() : $meter->lineItemsThisMonth();
                $lineItems->each(function ($item) use ($meter) {
                    $this->generateLineItem($item, $meter);
                });
                $this->invoice->load('lineItems');
            });

            $amount = round($this->invoice->lineItems->sum('amount'), 2);
            $total = round($this->invoice->lineItems->sum('total'), 2);

            $this->invoice->update([
                'amount' => $amount,
                'total' => $total,
                'vat' => $total - $amount,
                'due_at' => $this->lastMonth
                    ? now()->subMonth()->endOfMonth()->addDays(7)->format('Y-m-d')
                    : now()->endOfMonth()->addDays(7)->format('Y-m-d'),
                'is_final' => $lastMonth ? true : false,
            ]);

            return $this->invoice;
        }
    }

    private function invoice(): void
    {
        $hasNonInternalMeters = $this->property->meters()->where('is_internal', false)->count();

        $this->invoice = $this->property->invoices()->firstOrCreate([
            'date' => $this->lastMonth
                ? now()->subMonth()->endOfMonth()->format('Y-m-d')
                : now()->endOfMonth()->format('Y-m-d'),
        ], [
            'customer_id' => $this->property->customer_id,
            'number' => $hasNonInternalMeters ? Invoice::latest('id')->first()?->number + 1 ?? 1 : null,
        ]);
    }

    private function generateLineItem(array $item, Meter $meter): void
    {
        $baseAmount = $item['amount'];
        $vatAmount = round($baseAmount * 0.15, 2); // VAT is 15% of base amount
        $totalAmount = $baseAmount + $vatAmount;   // Total = base + VAT

        $lineItem = $this->invoice->lineItems()->firstOrNew([
            'description' => $item['description'],
            'invoiceable_id' => $meter->id,
            'invoiceable_type' => 'meter',
        ]);
        $lineItem->amount = $meter->is_internal ? 0 : $baseAmount;          // Base amount (excluding VAT)
        $lineItem->vat = $meter->is_internal ? 0 : $vatAmount;              // VAT amount only
        $lineItem->total = $meter->is_internal ? 0 : $totalAmount;          // Total including VAT
        $lineItem->product_id = $item['product_id'];
        $lineItem->price_id = $item['price_id'];
        $lineItem->quantity = $item['type'] === 'base_charge' ? 1 : $item['usage'];
        $lineItem->base_charge = $item['type'] === 'base_charge';
        $lineItem->invoiceable_id = $meter->id;
        $lineItem->invoiceable_type = 'meter';
        $lineItem->original_price = Price::find($item['price_id'])->amoount;
        $lineItem->date = $this->lastMonth
            ? now()->subMonth()->endOfMonth()->format('Y-m-d')
            : now()->endOfMonth()->format('Y-m-d');
        $lineItem->save();
    }

    private function propertyHasSpend(): bool
    {
        if ($this->property
            ->meters()
            ->whereHas('type', fn ($query) => $query->whereIn('name', ['Domestic Water', 'Single Phase Electricity', 'Three Phase Electricity']))
            ->count()
        ) {
            return true;
        }

        return false;
    }
}
