<?php

// app/Services/PaystackService.php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $client;

    protected $secretKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer '.config('services.paystack.secret_key'),
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
        $this->secretKey = config('services.paystack.secret_key');
    }

    public function initializeTransaction(array $data)
    {
        $response = $this->client->post('/transaction/initialize', [
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function verifyTransaction($reference)
    {
        $response = $this->client->get("/transaction/verify/{$reference}");

        return json_decode($response->getBody(), true);
    }

    /**
     * Process refund for a transaction
     *
     * @param  array  $data  - Must contain 'transaction' (transaction ID or reference) and optionally 'amount'
     * @return array
     */
    public function refundTransaction(array $data)
    {
        try {
            Log::info('Initiating Paystack refund', [
                'transaction' => $data['transaction'],
                'amount' => $data['amount'] ?? 'full',
            ]);

            $response = $this->client->post('/refund', [
                'json' => $data,
            ]);

            $result = json_decode($response->getBody(), true);

            Log::info('Paystack refund response', [
                'status' => $result['status'] ?? 'unknown',
                'data' => $result,
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Paystack refund failed', [
                'error' => $e->getMessage(),
                'transaction' => $data['transaction'] ?? 'unknown',
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get refund status
     *
     * @param  string  $reference
     * @return array
     */
    public function getRefundStatus($reference)
    {
        try {
            $response = $this->client->get("/refund/{$reference}");

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Failed to get Paystack refund status', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
