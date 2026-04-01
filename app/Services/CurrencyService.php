<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\User;
use App\Models\UserCurrencyPreference;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    const BASE_CURRENCY = 'NGN';

    /**
     * Default supported currencies (used as fallback reference).
     * The authoritative list is the `currencies` table — see getSupportedCodes().
     */
    const SUPPORTED_CURRENCIES = ['NGN', 'USD', 'GBP', 'EUR', 'GHS', 'ZAR', 'CAD'];

    const IP_API_URL = 'http://ip-api.com/json';

    const EXCHANGE_RATE_API_URL = 'https://api.exchangerate-api.com/v4/latest';

    const CACHE_DURATION_MINUTES = 60;

    const COUNTRY_TO_CURRENCY = [
        'NG' => 'NGN',
        'US' => 'USD',
        'GB' => 'GBP',
        'DE' => 'EUR',
        'FR' => 'EUR',
        'IT' => 'EUR',
        'GH' => 'GHS',
        'ZA' => 'ZAR',
        'CA' => 'CAD',
    ];

    /**
     * Detect user's currency based on IP address.
     * Falls back to NGN if detection fails.
     *
     * @param  string|null  $ip  IP to check; defaults to the current request IP
     * @return string Currency code (NGN, USD, GBP, etc.)
     */
    public function detectCurrencyFromIP(?string $ip = null): string
    {
        try {
            $ip = $ip ?? $this->getClientIP();

            if (! $ip || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return self::BASE_CURRENCY;
            }

            return Cache::remember("ip_currency_{$ip}", now()->addHours(24), function () use ($ip) {
                $response = Http::timeout(3)->get(self::IP_API_URL.'/'.$ip);

                if (! $response->successful()) {
                    return self::BASE_CURRENCY;
                }

                $data = $response->json();

                if (($data['status'] ?? '') !== 'success') {
                    return self::BASE_CURRENCY;
                }

                $countryCode = $data['countryCode'] ?? null;

                if (! $countryCode) {
                    return self::BASE_CURRENCY;
                }

                // ── Build mapping dynamically from DB ─────────────────────────
                // Cached separately so it refreshes when admin adds currencies
                $mapping = Cache::remember('country_to_currency_map', now()->addMinutes(self::CACHE_DURATION_MINUTES), function () {
                    $map = [];
                    Currency::active()
                        ->whereNotNull('country_codes')
                        ->get(['code', 'country_codes'])
                        ->each(function ($currency) use (&$map) {
                            foreach ((array) $currency->country_codes as $cc) {
                                $map[strtoupper($cc)] = $currency->code;
                            }
                        });

                    // Hardcoded values are the fallback — DB entries override them
                    return array_merge(self::COUNTRY_TO_CURRENCY, $map);
                });

                return $mapping[$countryCode] ?? self::BASE_CURRENCY;
            });

        } catch (\Exception $e) {
            Log::warning('Currency detection from IP failed', ['error' => $e->getMessage()]);

            return self::BASE_CURRENCY;
        }
    }

    /**
     * Get client's IP address, respecting proxy headers.
     */
    private function getClientIP(): ?string
    {
        $request = request();

        // Check for proxy headers (Cloudflare, ngrok, etc.)
        if ($ip = $request->header('CF-Connecting-IP')) {
            return $ip;
        }
        if ($ip = $request->header('X-Forwarded-For')) {
            return trim(explode(',', $ip)[0]);
        }
        if ($ip = $request->header('X-Real-IP')) {
            return $ip;
        }

        return $request->ip();
    }

    /**
     * Get or set user currency.
     * Re-detects from IP if the client IP has changed since last detection.
     */
    public function getUserCurrency(bool $autoDetect = true): string
    {
        $user = auth()->user();

        if ($user) {
            $pref = UserCurrencyPreference::where('user_id', $user->id)->first();
            if ($pref) {
                return $pref->currency_code;
            }
        }

        $currentIP = $this->getClientIP();
        $sessionCurrency = session('user_currency');
        $sessionIP = session('user_currency_ip');

        // Only trust the cached session value if the IP hasn't changed
        if ($sessionCurrency && $sessionIP === $currentIP) {
            return $sessionCurrency;
        }

        // IP changed or first visit or legacy session (no IP stored) — re-detect
        if ($autoDetect) {
            $detected = $this->detectCurrencyFromIP($currentIP);
            $this->setCurrencyInSession($detected, $currentIP);

            return $detected;
        }

        return self::BASE_CURRENCY;
    }

    /**
     * Get all active currency codes from the database.
     * This is the authoritative list — new currencies created in the admin
     * are automatically included.
     *
     * @return array<string>
     */
    public function getSupportedCodes(): array
    {
        return Cache::remember('currency_supported_codes', now()->addMinutes(self::CACHE_DURATION_MINUTES), function () {
            return Currency::active()->pluck('code')->all();
        });
    }

    /**
     * Set currency for authenticated user or guest.
     * Stores in DB for auth users, always saves to session.
     */
    public function setUserCurrency(string $currencyCode, ?User $user = null): void
    {
        if (! in_array($currencyCode, $this->getSupportedCodes())) {
            $currencyCode = self::BASE_CURRENCY;
        }

        $user = $user ?? auth()->user();

        if ($user) {
            UserCurrencyPreference::updateOrCreate(
                ['user_id' => $user->id],
                ['currency_code' => $currencyCode]
            );
            Cache::forget("user_currency_{$user->id}");
            Log::info("Currency updated for user {$user->id}: {$currencyCode}");
        }

        // Always persist to session — covers guests and acts as a fast-path
        // fallback for authenticated users on the same request.
        $this->setCurrencyInSession($currencyCode);
    }

    /**
     * Store currency in session for guests.
     */
    public function setCurrencyInSession(string $currencyCode, ?string $ip = null): void
    {
        if (in_array($currencyCode, $this->getSupportedCodes())) {
            session([
                'user_currency' => $currencyCode,
                'user_currency_ip' => $ip ?? $this->getClientIP(),
            ]);
        }
    }

    /**
     * Convert price from NGN to target currency.
     *
     * @param  int|float  $priceNGN  Price in Nigerian Naira
     * @param  string  $targetCurrency  Target currency code
     * @return float Converted price
     */
    public function convertFromNGN($priceNGN, string $targetCurrency): float
    {
        if ($targetCurrency === self::BASE_CURRENCY) {
            return (float) $priceNGN;
        }

        $rate = $this->getExchangeRate(self::BASE_CURRENCY, $targetCurrency);
        $converted = $priceNGN * $rate;

        // Apply additive markup (not multiplier)
        $markup = $this->getMarkupAmount($targetCurrency);

        return (float) ($converted + $markup);
    }

    /**
     * Convert price from source currency to NGN.
     *
     * @param  int|float  $price  Price in source currency
     * @param  string  $sourceCurrency  Source currency code
     * @return float Price in NGN
     */
    public function convertToNGN($price, string $sourceCurrency): float
    {
        if ($sourceCurrency === self::BASE_CURRENCY) {
            return (float) $price;
        }

        // Subtract additive markup first
        $markup = $this->getMarkupAmount($sourceCurrency);
        $priceWithoutMarkup = $price - $markup;

        // Get rate from source to NGN
        $rate = $this->getExchangeRate($sourceCurrency, self::BASE_CURRENCY);

        return (float) ($priceWithoutMarkup * $rate);
    }

    /**
     * Convert between two arbitrary currencies.
     *
     * @param  int|float  $amount  Amount to convert
     * @param  string  $from  Source currency
     * @param  string  $to  Target currency
     * @return float Converted amount
     */
    public function convert($amount, string $from, string $to): float
    {
        if ($from === $to) {
            return (float) $amount;
        }

        // Convert to NGN first, then to target
        $amountNGN = $this->convertToNGN($amount, $from);

        return $this->convertFromNGN($amountNGN, $to);
    }

    /**
     * Get exchange rate between two currencies (using NGN as base).
     * Rate is cached in memory for 1 hour.
     *
     * @return float Exchange rate
     */
    public function getExchangeRate(string $from, string $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $cacheKey = "exchange_rate_{$from}_{$to}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($from, $to) {
            // If converting from or to NGN, query the database
            if ($from === self::BASE_CURRENCY) {
                $currency = Currency::where('code', $to)->with('latestRate')->first();

                return (float) ($currency?->latestRate?->rate ?? 1.0);
            }

            if ($to === self::BASE_CURRENCY) {
                $currency = Currency::where('code', $from)->with('latestRate')->first();
                $rate = (float) ($currency?->latestRate?->rate ?? 1.0);

                return $rate > 0 ? (1.0 / $rate) : 1.0;
            }

            // For other conversions, convert through NGN
            $fromToNGN = $this->getExchangeRate($from, self::BASE_CURRENCY);
            $ngnToTarget = $this->getExchangeRate(self::BASE_CURRENCY, $to);

            return $fromToNGN * $ngnToTarget;
        });
    }

    /**
     * Get additive markup amount for a currency (excluding NGN).
     * Markup is stored in database as additive value (not multiplier).
     *
     * @return float Markup amount (0 for NGN)
     */
    public function getMarkupAmount(string $currency): float
    {
        if ($currency === self::BASE_CURRENCY) {
            return 0.0;
        }

        return Cache::remember("currency_markup_{$currency}", now()->addHours(1), function () use ($currency) {
            $curr = Currency::where('code', $currency)->first();

            return (float) ($curr?->markup ?? 0.0);
        });
    }

    /**
     * Format price with currency symbol.
     *
     * @param  int|float  $amount  Price amount
     * @param  string  $currency  Currency code
     * @param  bool  $convertFromNGN  Whether to convert from NGN first
     * @return string Formatted price (e.g., "₦28,500" or "$18.50")
     */
    public function format($amount, ?string $currency = null, bool $convertFromNGN = false): string
    {
        $currency = $currency ?? $this->getUserCurrency();

        if ($convertFromNGN && $currency !== self::BASE_CURRENCY) {
            $amount = $this->convertFromNGN($amount, $currency);
        }

        $symbol = $this->getSymbol($currency);
        $formatted = number_format((float) $amount, 2, '.', ',');

        return "{$symbol}{$formatted}";
    }

    /**
     * Get currency symbol.
     */
    public function getSymbol(string $currency): string
    {
        return Cache::remember("currency_symbol_{$currency}", now()->addHours(24), function () use ($currency) {
            $curr = Currency::where('code', $currency)->first();

            return $curr?->symbol ?? $currency;
        });
    }

    /**
     * Return the currency config array for Alpine store injection.
     * Shape: { active, rates, markup, symbols }
     *
     * rates/markup/symbols are cached for 60 minutes (shared across all users).
     * active is resolved per-request from the user's session/DB preference so
     * that switching currencies always survives a page refresh.
     *
     * @return array{active: string, rates: array<string, float>, markup: array<string, float>, symbols: array<string, string>}
     */
    public function getAlpineStoreData(): array
    {
        // Per-user — never cached globally.
        $userCurrency = $this->getUserCurrency();

        // Static data shared across users — safe to cache.
        $static = Cache::remember('currency_store_data', now()->addMinutes(self::CACHE_DURATION_MINUTES), function () {
            $currencies = Currency::active()
                ->with('latestRate')
                ->get();

            $rates = [];
            $markups = [];
            $symbols = [];

            foreach ($currencies as $currency) {
                $rates[$currency->code] = (float) ($currency->latestRate?->rate ?? 1.0);
                // markup is an additive amount in the foreign currency (0 = no markup)
                $markups[$currency->code] = (float) $currency->markup;
                $symbols[$currency->code] = $currency->symbol;
            }

            return compact('rates', 'markups', 'symbols');
        });

        return [
            'active' => $userCurrency,
            'rates' => $static['rates'],
            'markup' => $static['markups'],
            'symbols' => $static['symbols'],
        ];
    }

    /**
     * Fetch exchange rates from external API and update database.
     * Should be called by scheduled command or manually.
     */
    public function updateExchangeRates(): void
    {
        try {
            Log::info('Updating exchange rates from external API...');

            $response = Http::timeout(10)->get(self::EXCHANGE_RATE_API_URL.'/'.self::BASE_CURRENCY);

            if (! $response->successful()) {
                Log::error('Exchange rate API request failed', ['status' => $response->status()]);

                return;
            }

            $data = $response->json();
            $rates = $data['rates'] ?? [];

            // Use DB currencies so newly admin-created currencies get rates too.
            $dbCurrencies = Currency::where('code', '!=', self::BASE_CURRENCY)->get();
            foreach ($dbCurrencies as $currency) {
                if (isset($rates[$currency->code])) {
                    $currency->exchangeRates()->create([
                        'rate' => $rates[$currency->code],
                        'fetched_at' => now(),
                    ]);
                    Log::info("Updated {$currency->code} rate: {$rates[$currency->code]}");
                }
            }

            $this->clearCache();
            Log::info('Exchange rates updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update exchange rates', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Flush the cached currency data (call after admin updates a rate or markup).
     */
    public function clearCache(): void
    {
        Cache::forget('currency_store_data');
        Cache::forget('currency_supported_codes');
        Cache::forget('country_to_currency_map');  // ← add this

        $codes = Currency::pluck('code')->all();
        foreach ($codes as $code) {
            Cache::forget("currency_markup_{$code}");
            Cache::forget("currency_symbol_{$code}");
            Cache::forget("exchange_rate_NGN_{$code}");
            Cache::forget("exchange_rate_{$code}_NGN");
        }
    }
}
