<?php

// app/Services/FlutterwaveService.php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    protected $client;

    protected $publicKey;

    protected $secretKey;

    protected $encryptionKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.flutterwave.com/v3',
            'headers' => [
                'Authorization' => 'Bearer '.config('services.flutterwave.secret_key'),
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);

        $this->publicKey = config('services.flutterwave.public_key');
        $this->secretKey = config('services.flutterwave.secret_key');
        $this->encryptionKey = config('services.flutterwave.encryption_key');
    }

    public function initializePayment(array $data)
    {
        try {
            $response = $this->client->post('/v3/payments', [
                'json' => $data,
            ]);

            $result = json_decode($response->getBody(), true);

            Log::info('Flutterwave API Response', [
                'status' => $result['status'] ?? 'unknown',
                'data' => $result,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Flutterwave initialization error', [
                'message' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function verifyTransaction($transactionId)
    {
        try {
            $response = $this->client->get("/v3/transactions/{$transactionId}/verify");

            $result = json_decode($response->getBody(), true);

            Log::info('Flutterwave verification response', [
                'transaction_id' => $transactionId,
                'status' => $result['status'] ?? 'unknown',
                'data' => $result,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Flutterwave verification error', [
                'message' => $e->getMessage(),
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify transaction by reference (tx_ref)
     * Uses the correct Flutterwave endpoint with query parameter
     *
     * @param  string  $txRef
     * @return array
     */
    public function verifyTransactionByReference($txRef)
    {
        try {
            Log::info('Verifying Flutterwave transaction by reference', [
                'tx_ref' => $txRef,
                'endpoint' => '/v3/transactions/verify_by_reference',
            ]);

            // Correct endpoint with query parameter
            $response = $this->client->get('/v3/transactions/verify_by_reference', [
                'query' => ['tx_ref' => $txRef],
            ]);

            $result = json_decode($response->getBody(), true);

            Log::info('Flutterwave verification by reference response', [
                'tx_ref' => $txRef,
                'status' => $result['status'] ?? 'unknown',
                'has_data' => isset($result['data']),
                'data_id' => $result['data']['id'] ?? null,
            ]);

            return $result;

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $bodyData = json_decode($body, true);

            Log::error('Flutterwave verification by reference client error', [
                'message' => $e->getMessage(),
                'status_code' => $statusCode,
                'response_body' => $bodyData,
                'tx_ref' => $txRef,
            ]);

            throw $e;
        } catch (\Exception $e) {
            Log::error('Flutterwave verification by reference error', [
                'message' => $e->getMessage(),
                'tx_ref' => $txRef,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Process refund for a transaction
     *
     * @param  int|string  $transactionIdentifier  - Flutterwave transaction ID (numeric) or reference
     * @param  float|null  $amount  - Optional amount to refund (null for full refund)
     * @return array
     */
    public function refundTransaction($transactionIdentifier, $amount = null)
    {
        try {
            Log::info('Initiating Flutterwave refund', [
                'transaction_identifier' => $transactionIdentifier,
                'identifier_type' => is_numeric($transactionIdentifier) ? 'numeric_id' : 'reference',
                'amount' => $amount ?? 'full',
            ]);

            // Validate transaction identifier
            if (empty($transactionIdentifier)) {
                throw new \Exception('Transaction identifier is required for Flutterwave refunds');
            }

            // If identifier is not numeric, we need to look up the transaction first
            $transactionId = $transactionIdentifier;
            if (! is_numeric($transactionIdentifier)) {
                Log::info('Non-numeric identifier provided, looking up transaction by reference', [
                    'reference' => $transactionIdentifier,
                ]);

                // Use verify_by_reference to get the numeric ID
                $verifyResponse = $this->verifyTransactionByReference($transactionIdentifier);

                if ($verifyResponse['status'] === 'success' && isset($verifyResponse['data']['id'])) {
                    $transactionId = $verifyResponse['data']['id'];
                    Log::info('Found numeric transaction ID', [
                        'reference' => $transactionIdentifier,
                        'transaction_id' => $transactionId,
                    ]);
                } else {
                    throw new \Exception('Could not find transaction with reference: '.$transactionIdentifier);
                }
            }

            $payload = [];
            if ($amount !== null) {
                $payload['amount'] = (float) $amount;
            }

            // Use the numeric transaction ID for refund
            Log::info('Calling Flutterwave refund API', [
                'transaction_id' => $transactionId,
                'endpoint' => "/v3/transactions/{$transactionId}/refund",
            ]);

            $response = $this->client->post("/v3/transactions/{$transactionId}/refund", [
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody(), true);

            Log::info('Flutterwave refund response', [
                'status' => $result['status'] ?? 'unknown',
                'data' => $result,
            ]);

            return $result;

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $bodyData = json_decode($body, true);

            Log::error('Flutterwave refund client error', [
                'error' => $e->getMessage(),
                'status_code' => $statusCode,
                'response_body' => $bodyData,
                'transaction_identifier' => $transactionIdentifier,
            ]);

            return [
                'status' => 'error',
                'message' => $bodyData['message'] ?? $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error('Flutterwave refund failed', [
                'error' => $e->getMessage(),
                'transaction_identifier' => $transactionIdentifier,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get refund status
     *
     * @param  string  $transactionId
     * @return array
     */
    public function getRefundStatus($transactionId)
    {
        try {
            $response = $this->client->get("/v3/transactions/{$transactionId}/refund");
            $result = json_decode($response->getBody(), true);

            Log::info('Flutterwave refund status', [
                'transaction_id' => $transactionId,
                'data' => $result,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to get Flutterwave refund status', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
