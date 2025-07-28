<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportXeroInvoice implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->invoices as $invoice) {
            $invoiceRows = $this->convertInvoiceToRows($invoice);
            foreach ($invoiceRows as $row) {
                $rows->push($row);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '*ContactName',
            'EmailAddress',
            'POAddressLine1',
            'POAddressLine2',
            'POAddressLine3',
            'POAddressLine4',
            'POCity',
            'PORegion',
            'POPostalCode',
            'POCountry',
            '*InvoiceNumber',
            'Reference',
            '*InvoiceDate',
            '*DueDate',
            'Total',
            'InventoryItemCode',
            '*Description',
            '*Quantity',
            '*UnitAmount',
            'Discount',
            '*AccountCode',
            '*TaxType',
            'TaxAmount',
            'TrackingName1',
            'TrackingOption1',
            'TrackingName2',
            'TrackingOption2',
            'Currency',
            'BrandingTheme',
        ];
    }

    public function map($row): array
    {
        return $row;
    }

    public function title(): string
    {
        return 'Xero Invoice Export';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE0E0E0',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Auto-size columns
            'A:AC' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
        ];
    }

    private function convertInvoiceToRows(Invoice $invoice): array
    {
        $customer = $invoice->customer;
        $property = $invoice->property;
        $rows = [];

        if ($invoice->lineItems->isNotEmpty()) {
            foreach ($invoice->lineItems as $index => $lineItem) {
                if ($lineItem->amount < 0 || $lineItem->amount > 0) {
                    $row = [
                        $customer->display_name, // *ContactName
                        $customer->billing_email ?: $customer->user?->email ?? '', // EmailAddress
                        $this->getAddressLine($property, 1), // POAddressLine1
                        $this->getAddressLine($property, 2), // POAddressLine2
                        $this->getAddressLine($property, 3), // POAddressLine3
                        $this->getAddressLine($property, 4), // POAddressLine4
                        $property?->city ?: $customer->city ?? '', // POCity
                        $property?->province ?: $customer->province ?? '', // PORegion
                        $property?->postal_code ?: $customer->postal_code ?? '', // POPostalCode
                        'South Africa', // POCountry
                        $invoice->display_number, // *InvoiceNumber
                        $property?->name.' - '.$invoice->date->format('Y-m-d'), // Reference
                        $invoice->date->format('Y-m-d'), // *InvoiceDate
                        $invoice->due_at->format('Y-m-d'), // *DueDate
                        $index === 0 ? number_format($invoice->total, 2, '.', '') : '', // Total
                        $lineItem->price?->inventory_code ?? '', // InventoryItemCode
                        $lineItem->description.' '.$lineItem->invoiceable?->serial_number, // *Description
                        1, // *Quantity
                        number_format($lineItem->amount, 2, '.', ''), // *UnitAmount
                        $lineItem->discount > 0 ? number_format($lineItem->discount, 2, '.', '') : '', // Discount
                        $this->getAccountCode($lineItem), // *AccountCode
                        $this->getTaxType($lineItem), // *TaxType
                        number_format($lineItem->vat, 2, '.', ''), // TaxAmount
                        'Service', // TrackingName1
                        $lineItem->service?->name ?? '', // TrackingOption1
                        'Property', // TrackingName2
                        $property?->name ?? $property?->id ?? '', // TrackingOption2
                        'ZAR', // Currency
                        '', // BrandingTheme
                    ];

                    $rows[] = $row;
                }
            }
        } else {
            // Invoice without line items - create single row
            if ($invoice->total < 0 || $invoice->total > 0) {
                $row = [
                    $customer->display_name, // *ContactName
                    $customer->billing_email ?: $customer->user?->email ?? '', // EmailAddress
                    $this->getAddressLine($property, 1), // POAddressLine1
                    $this->getAddressLine($property, 2), // POAddressLine2
                    $this->getAddressLine($property, 3), // POAddressLine3
                    $this->getAddressLine($property, 4), // POAddressLine4
                    $property?->city ?: $customer->city ?? '', // POCity
                    $property?->province ?: $customer->province ?? '', // PORegion
                    $property?->postal_code ?: $customer->postal_code ?? '', // POPostalCode
                    'South Africa', // POCountry
                    $invoice->display_number, // *InvoiceNumber
                    $property?->name.' - '.$invoice->date->format('Y-m-d'), // Reference
                    $invoice->date->format('Y-m-d'), // *InvoiceDate
                    $invoice->due_at->format('Y-m-d'), // *DueDate
                    number_format($invoice->total, 2, '.', ''), // Total
                    '', // InventoryItemCode
                    'Invoice '.$invoice->display_number, // *Description
                    '1.00', // *Quantity
                    number_format($invoice->amount, 2, '.', ''), // *UnitAmount
                    $invoice->discount > 0 ? number_format($invoice->discount, 2, '.', '') : '', // Discount
                    '', // *AccountCode
                    $invoice->vat_rate > 0 ? 'Standard Rate Sales' : 'Standard Rate Sales', // *TaxType
                    number_format($invoice->vat, 2, '.', ''), // TaxAmount
                    'Property', // TrackingName1
                    $property?->name ?? $property?->id ?? '', // TrackingOption1
                    '', // TrackingName2
                    '', // TrackingOption2
                    'ZAR', // Currency
                    '', // BrandingTheme
                ];

                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function getAddressLine($property, int $lineNumber): string
    {
        if (! $property) {
            return '';
        }

        return match ($lineNumber) {
            1 => $property->street ?? '',
            2 => $property->suburb ?? '',
            3 => '',
            4 => '',
            default => ''
        };
    }

    private function getAccountCode($lineItem): string
    {
        return $lineItem->price?->product_code ?? '';
    }

    private function getTaxType($lineItem): string
    {
        // Calculate tax rate percentage
        if ($lineItem->vat > 0 && $lineItem->amount > 0) {
            $taxRate = ($lineItem->vat / $lineItem->amount) * 100;
            if (abs($taxRate - 15) < 0.1) { // Within 0.1% of 15%
                return 'Standard Rate Sales'; // 15% VAT
            }
        }

        return 'Standard Rate Sales'; // 0% VAT
    }
}
