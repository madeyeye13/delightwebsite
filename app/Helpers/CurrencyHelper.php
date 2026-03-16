<?php

use App\Services\CurrencyService;

if (! function_exists('currency')) {
    /**
     * Get the CurrencyService instance.
     */
    function currency(): CurrencyService
    {
        return app(CurrencyService::class);
    }
}

if (! function_exists('format_price')) {
    /**
     * Format price with currency symbol.
     *
     * @param  int|float  $amount  Price amount in NGN
     * @param  string|null  $currency  Currency code (defaults to user's selected)
     * @param  bool  $convert  Whether to convert from NGN
     * @return string Formatted price
     */
    function format_price($amount, ?string $currency = null, bool $convert = true): string
    {
        return currency()->format($amount, $currency, $convert);
    }
}

if (! function_exists('convert_price')) {
    /**
     * Convert price between currencies.
     *
     * @param  int|float  $amount  Amount to convert
     * @param  string  $from  Source currency
     * @param  string  $to  Target currency
     * @return float Converted amount
     */
    function convert_price($amount, string $from, string $to): float
    {
        return currency()->convert($amount, $from, $to);
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Get symbol for a currency.
     */
    function currency_symbol(string $code): string
    {
        return currency()->getSymbol($code);
    }
}

if (! function_exists('user_currency')) {
    /**
     * Get current user's selected currency.
     */
    function user_currency(): string
    {
        return currency()->getUserCurrency();
    }
}
