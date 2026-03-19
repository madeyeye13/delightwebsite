<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use App\Services\OrderService;
use App\Services\PaystackService;
use App\Services\FlutterwaveService;
use App\Services\ReferralService;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaystackService $paystackService,
        private readonly FlutterwaveService $flutterwaveService,
        private readonly ReferralService $referralService, // ✅ Injected once
    ) {}

    public function paystack(Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('checkout.index')
                ->with('error', 'Invalid payment reference.');
        }

        try {
            $response = $this->paystackService->verifyTransaction($reference);

            if (
                isset($response['status'], $response['data']['status']) &&
                $response['status'] === true &&
                $response['data']['status'] === 'success'
            ) {
                $order = Order::where('order_number', $reference)->first();

                if ($order && $order->payment_status !== 'paid') {

                    // ✅ Mark order as paid
                    $this->orderService->markOrderPaid($order, $reference);

                    // ✅ Process referral + points (only once)
                    if (! $order->referral_processed) {
                        $this->referralService->processReferralForOrder($order, $request);
                        $this->referralService->processPointsRedemption($order);

                        $order->update([
                            'referral_processed' => true
                        ]);
                    }
                }

                return redirect()->route('checkout.success', [
                    'orderNumber' => $order?->order_number ?? $reference
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Paystack callback error', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment could not be verified. Please contact support.');
    }

    public function flutterwave(Request $request): RedirectResponse
    {
        $status = $request->query('status');
        $txRef = $request->query('tx_ref');

        if ($status !== 'successful' || ! $txRef) {
            return redirect()->route('checkout.index')
                ->with('error', 'Payment was not completed.');
        }

        try {
            $response = $this->flutterwaveService->verifyTransactionByReference($txRef);

            if (
                isset($response['status'], $response['data']['status']) &&
                $response['status'] === 'success' &&
                $response['data']['status'] === 'successful'
            ) {
                $order = Order::where('order_number', $txRef)->first();

                if ($order && $order->payment_status !== 'paid') {

                    // ✅ Mark order as paid
                    $this->orderService->markOrderPaid($order, $txRef);

                    // ✅ Process referral + points (only once)
                    if (! $order->referral_processed) {
                        $this->referralService->processReferralForOrder($order, $request);
                        $this->referralService->processPointsRedemption($order);

                        $order->update([
                            'referral_processed' => true
                        ]);
                    }
                }

                return redirect()->route('checkout.success', [
                    'orderNumber' => $order?->order_number ?? $txRef
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Flutterwave callback error', [
                'tx_ref' => $txRef,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment could not be verified. Please contact support.');
    }
}