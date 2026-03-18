<?php

namespace App\Services;

use App\Models\DhlConfiguration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DHLService
{
    private string $baseUrl;

    private string $username;

    private string $password;

    private string $accountNumber;

    private float $markupPercentage;

    public function __construct()
    {
        $this->baseUrl = app()->environment('local') || DhlConfiguration::get('test_mode', true)
            ? 'https://express.api.dhl.com/mydhlapi/test'
            : rtrim(config('services.dhl.base_url', 'https://express.api.dhl.com/mydhlapi'), '/');

        $this->username = config('services.dhl.api_username');
        $this->password = config('services.dhl.api_password');
        $this->accountNumber = config('services.dhl.account_number');
        $this->markupPercentage = (float) DhlConfiguration::get('markup_percentage', 15);
    }

    public function isConfigured(): bool
    {
        return ! empty($this->username) && ! empty($this->password) && ! empty($this->accountNumber);
    }

    /**
     * Get shipping rates from DHL for a destination.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function getRates(array $params): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'not_configured' => true,
                'error' => 'DHL account not yet configured.',
            ];
        }

        try {
            if (empty($params['destination_country_code']) || strlen($params['destination_country_code']) !== 2) {
                return ['success' => false, 'error' => 'Invalid destination country code.'];
            }

            if ($params['weight'] < 0.5) {
                $params['weight'] = 0.5;
            }

            $payload = $this->buildRatePayload($params);

            $response = Http::timeout(30)
                ->withBasicAuth($this->username, $this->password)
                ->withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                ->post("{$this->baseUrl}/rates", $payload);

            if ($response->failed()) {
                $errorBody = $response->json();
                Log::error('DHL Rate Request Failed', ['status' => $response->status(), 'error' => $errorBody]);
                $msg = $errorBody['detail'] ?? $errorBody['message'] ?? 'Failed to get DHL rates.';

                return ['success' => false, 'error' => $msg, 'status_code' => $response->status()];
            }

            $data = $response->json();

            if (empty($data['products'])) {
                return ['success' => false, 'error' => 'No DHL shipping options available for this destination.'];
            }

            return [
                'success' => true,
                'products' => $this->processRateProducts($data['products'], $params['currency'] ?? 'NGN'),
            ];
        } catch (\Exception $e) {
            Log::error('DHL Rate Exception', ['message' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Connection error: '.$e->getMessage()];
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @return array<int, array<string, mixed>>
     */
    private function processRateProducts(array $products, string $targetCurrency): array
    {
        $processed = [];
        foreach ($products as $product) {
            $basePrice = $this->extractPrice($product['totalPrice'] ?? [], $targetCurrency);
            if (! $basePrice) {
                continue;
            }
            $markupAmount = ($basePrice * $this->markupPercentage) / 100;
            $finalPrice = $basePrice + $markupAmount;

            $processed[] = [
                'product_name' => $product['productName'] ?? '',
                'product_code' => $product['productCode'] ?? '',
                'base_price' => round($basePrice, 2),
                'markup_percentage' => $this->markupPercentage,
                'markup_amount' => round($markupAmount, 2),
                'final_price' => round($finalPrice, 2),
                'currency' => $targetCurrency,
                'estimated_delivery_date' => $product['deliveryCapabilities']['estimatedDeliveryDateAndTime'] ?? null,
                'total_transit_days' => $product['deliveryCapabilities']['totalTransitDays'] ?? null,
            ];
        }

        return $processed;
    }

    /** @param  array<int, array<string, mixed>>  $totalPriceArray */
    private function extractPrice(array $totalPriceArray, string $targetCurrency): ?float
    {
        foreach ($totalPriceArray as $item) {
            if (($item['priceCurrency'] ?? '') === $targetCurrency && ($item['currencyType'] ?? '') === 'BILLC') {
                return (float) $item['price'];
            }
        }
        foreach ($totalPriceArray as $item) {
            if (($item['priceCurrency'] ?? '') === $targetCurrency) {
                return (float) $item['price'];
            }
        }
        // Fallback: convert from any available currency
        $currencyService = app(CurrencyService::class);
        foreach ($totalPriceArray as $item) {
            if (in_array($item['priceCurrency'] ?? '', ['EUR', 'NGN', 'USD'])) {
                return $currencyService->convert((float) $item['price'], $item['priceCurrency'], $targetCurrency);
            }
        }

        return null;
    }

    /** @param  array<string, mixed>  $params */
    private function buildRatePayload(array $params): array
    {
        $postalCode = $params['destination_postal_code'] ?? '';
        if ($params['destination_country_code'] === 'US') {
            $postalCode = preg_replace('/[^0-9]/', '', $postalCode);
            if (strlen($postalCode) > 5) {
                $postalCode = substr($postalCode, 0, 5);
            }
        }
        if (empty($postalCode) || strlen($postalCode) < 3) {
            $postalCode = null;
        }

        $pickupDate = $this->nextBusinessDay(now()->addDays(3));
        $plannedShippingDate = $pickupDate->setTime(10, 0, 0)->format('Y-m-d\TH:i:s\G\M\TP');

        $receiverDetails = [
            'cityName' => $params['destination_city'] ?? '',
            'countryCode' => $params['destination_country_code'],
        ];
        if ($postalCode) {
            $receiverDetails['postalCode'] = $postalCode;
        }

        return [
            'plannedShippingDateAndTime' => $plannedShippingDate,
            'productCode' => 'P',
            'accounts' => [['typeCode' => 'shipper', 'number' => $this->accountNumber]],
            'customerDetails' => [
                'shipperDetails' => [
                    'postalCode' => config('services.dhl.origin.postal_code', '100281'),
                    'cityName' => config('services.dhl.origin.city', 'Lagos'),
                    'countryCode' => config('services.dhl.origin.country_code', 'NG'),
                ],
                'receiverDetails' => $receiverDetails,
            ],
            'unitOfMeasurement' => 'metric',
            'isCustomsDeclarable' => true,
            'monetaryAmount' => [[
                'typeCode' => 'declaredValue',
                'value' => $params['declared_value'] ?? 100,
                'currency' => $params['currency'] ?? 'USD',
            ]],
            'packages' => [[
                'weight' => max(0.5, (float) ($params['weight'] ?? 1.5)),
                'dimensions' => [
                    'length' => DhlConfiguration::get('default_length_cm', 30),
                    'width' => DhlConfiguration::get('default_width_cm', 30),
                    'height' => DhlConfiguration::get('default_height_cm', 10),
                ],
            ]],
        ];
    }

    private function nextBusinessDay(Carbon $date, int $daysToAdd = 0): Carbon
    {
        $date = $date->copy();
        $added = 0;
        while ($added < $daysToAdd || $date->isWeekend()) {
            if (! $date->isWeekend()) {
                $added++;
            }
            if ($added < $daysToAdd || $date->isWeekend()) {
                $date->addDay();
            }
        }

        return $date;
    }
}
