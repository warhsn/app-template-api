<?php

if (! function_exists('asCurrency')) {
    /**
     * Helper function format money.
     */
    function asCurrency(float $amount, string $currency = 'ZAR'): string
    {
        $formatter = new NumberFormatter('en_ZA', NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($amount, $currency);
    }
}

if (! function_exists('asMoney')) {
    /**
     * Helper function format money.
     */
    function asMoney(float $amount): string
    {
        return (string) number_format($amount, 2, '.', '');
    }
}
