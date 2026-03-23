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

    // ─── Rates ────────────────────────────────────────────────────────────────

    /** @param array<string, mixed> $params */
    public function getRates(array $params): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'not_configured' => true, 'error' => 'DHL account not yet configured.'];
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

    /** @param array<int, array<string, mixed>> $products */
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

    /** @param array<int, array<string, mixed>> $totalPriceArray */
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
        $currencyService = app(CurrencyService::class);
        foreach ($totalPriceArray as $item) {
            if (in_array($item['priceCurrency'] ?? '', ['EUR', 'NGN', 'USD'])) {
                return $currencyService->convert((float) $item['price'], $item['priceCurrency'], $targetCurrency);
            }
        }

        return null;
    }

    /** @param array<string, mixed> $params */
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
                    'postalCode' => config('services.dhl.origin.postal_code', '100001'),
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

    // ─── Shipment Creation ────────────────────────────────────────────────────

    /**
     * Create an actual DHL shipment and return tracking number + label PDF.
     *
     * @param  array<string, mixed>  $params  {
     *                                        receiver_name, receiver_email, receiver_phone,
     *                                        receiver_address, receiver_city, receiver_country_code, receiver_postal,
     *                                        total_weight, declared_value, currency,
     *                                        invoice_number, invoice_date,
     *                                        line_items: [{description, price, quantity, weight}]
     *                                        }
     */
    public function createShipment(array $params): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'DHL account not yet configured.'];
        }

        try {
            $payload = $this->buildShipmentPayload($params);

            Log::info('DHL Create Shipment Request', [
                'order_invoice' => $params['invoice_number'] ?? null,
                'destination' => $params['receiver_country_code'] ?? null,
            ]);

            $response = Http::timeout(60)
                ->withBasicAuth($this->username, $this->password)
                ->withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                ->post("{$this->baseUrl}/shipments", $payload);

            if ($response->failed()) {
                $errorBody = $response->json();
                Log::error('DHL Shipment Request Failed', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                ]);
                $msg = $errorBody['detail'] ?? $errorBody['message'] ?? 'Failed to create DHL shipment.';
                if (! empty($errorBody['additionalDetails'])) {
                    $msg .= ': '.implode(', ', (array) $errorBody['additionalDetails']);
                }

                return ['success' => false, 'error' => $msg, 'details' => $errorBody];
            }

            $data = $response->json();
            $trackingNumber = $data['shipmentTrackingNumber'] ?? null;

            Log::info('DHL Shipment Created', ['tracking_number' => $trackingNumber]);

            return [
                'success' => true,
                'tracking_number' => $trackingNumber,
                'tracking_url' => $trackingNumber
                    ? "https://www.dhl.com/en/express/tracking.html?AWB={$trackingNumber}"
                    : null,
                'shipment_id' => $data['shipmentId'] ?? $trackingNumber,
                'documents' => $data['documents'] ?? [],
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('DHL Shipment Exception', ['message' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Connection error: '.$e->getMessage()];
        }
    }

    /** @param array<string, mixed> $params */
    private function buildShipmentPayload(array $params): array
    {
        $pickupDate = $this->nextBusinessDay(now()->addDays(3));
        $plannedShippingDate = $pickupDate->setTime(10, 0, 0)->format('Y-m-d\TH:i:s\G\M\TP');

        // Postal code: DHL requires at least something; use a placeholder if missing
        $postalCode = (! empty($params['receiver_postal']) && strlen($params['receiver_postal']) >= 3)
            ? $params['receiver_postal']
            : '00000';

        return [
            'plannedShippingDateAndTime' => $plannedShippingDate,
            'pickup' => ['isRequested' => true],
            'productCode' => $params['product_code'] ?? 'P',
            'accounts' => [['typeCode' => 'shipper', 'number' => $this->accountNumber]],
            'customerDetails' => [
                'shipperDetails' => [
                    'postalAddress' => [
                        'postalCode' => config('services.dhl.origin.postal_code', '100001'),
                        'cityName' => config('services.dhl.origin.city', 'Lagos'),
                        'countryCode' => config('services.dhl.origin.country_code', 'NG'),
                        'addressLine1' => config('services.dhl.origin.address', '22 Latifat Salami Street'),
                    ],
                    'contactInformation' => [
                        'companyName' => config('services.dhl.origin.company_name', '1st Delightsome Fabrics'),
                        'fullName' => config('services.dhl.origin.company_name', '1st Delightsome Fabrics'),
                        'phone' => config('services.dhl.origin.phone'),
                        'email' => config('mail.from.address'),
                    ],
                ],
                'receiverDetails' => [
                    'postalAddress' => [
                        'postalCode' => $postalCode,
                        'cityName' => $params['receiver_city'],
                        'countryCode' => $params['receiver_country_code'], // must be 2-letter ISO code
                        'addressLine1' => $params['receiver_address'] ?? 'N/A',
                    ],
                    'contactInformation' => [
                        'companyName' => $params['receiver_name'],
                        'fullName' => $params['receiver_name'],
                        'phone' => $params['receiver_phone'],
                        'email' => $params['receiver_email'],
                    ],
                ],
            ],
            'content' => [
                'packages' => [[
                    'weight' => round(max((float) ($params['total_weight'] ?? 0.5), 0.5), 3),
                    'dimensions' => [
                        'length' => (float) DhlConfiguration::get('default_length_cm', 30),
                        'width' => (float) DhlConfiguration::get('default_width_cm', 30),
                        'height' => (float) DhlConfiguration::get('default_height_cm', 10),
                    ],
                    'description' => 'Fashion items',
                ]],
                'isCustomsDeclarable' => true,
                'declaredValue' => (float) max($params['declared_value'] ?? 10, 1),
                'declaredValueCurrency' => $params['currency'] ?? 'NGN',
                'description' => 'Fashion items',
                'incoterm' => 'DAP',
                'unitOfMeasurement' => 'metric',
                'exportDeclaration' => [
                    'invoice' => [
                        'number' => (string) ($params['invoice_number'] ?? 'INV-'.now()->format('YmdHis')),
                        'date' => (string) ($params['invoice_date'] ?? now()->format('Y-m-d')),
                    ],
                    'lineItems' => $this->buildLineItems($params['line_items'] ?? []),
                ],
            ],
            'outputImageProperties' => [
                'imageOptions' => [[
                    'typeCode' => 'label',
                    'templateName' => 'ECOM26_A6_002',
                ]],
            ],
        ];
    }

    /** @param array<int, array<string, mixed>> $items */
    private function buildLineItems(array $items): array
    {
        if (empty($items)) {
            return [[
                'number' => 1,
                'description' => 'Fashion items',
                'price' => 10.0,
                'quantity' => ['value' => 1, 'unitOfMeasurement' => 'PCS'],
                'commodityCodes' => [['typeCode' => 'outbound', 'value' => '6204']],
                'exportReasonType' => 'permanent',
                'manufacturerCountry' => 'NG',
                'weight' => ['netValue' => 0.5, 'grossValue' => 0.5],
            ]];
        }

        $result = [];
        foreach (array_values($items) as $i => $item) {
            $weight = (float) round(max($item['weight'] ?? 0.5, 0.1), 3);
            $result[] = [
                'number' => $i + 1,
                'description' => substr((string) ($item['description'] ?? 'Fashion item'), 0, 50),
                'price' => (float) max($item['price'] ?? 1.0, 0.01),
                'quantity' => [
                    'value' => (int) max($item['quantity'] ?? 1, 1),
                    'unitOfMeasurement' => 'PCS',
                ],
                'commodityCodes' => [['typeCode' => 'outbound', 'value' => '6204']],
                'exportReasonType' => 'permanent',
                'manufacturerCountry' => 'NG',
                'weight' => ['netValue' => $weight, 'grossValue' => $weight],
            ];
        }

        return $result;
    }

    // ─── Tracking ─────────────────────────────────────────────────────────────

    public function trackShipment(string $trackingNumber): array
    {
        try {
            $response = Http::timeout(15)
                ->withBasicAuth($this->username, $this->password)
                ->get("{$this->baseUrl}/tracking", ['trackingNumber' => $trackingNumber]);

            if ($response->failed()) {
                return ['success' => false, 'error' => 'Failed to fetch tracking data.'];
            }

            return ['success' => true, 'tracking_data' => $response->json()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─── Utilities ────────────────────────────────────────────────────────────

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

    public function getMarkupPercentage(): float
    {
        return $this->markupPercentage;
    }
}
