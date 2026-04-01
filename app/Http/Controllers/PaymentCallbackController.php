<?php

namespace App\Http\Controllers;

use App\Mail\AdminOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\AppSetting;
use App\Models\Order;
use App\Services\FlutterwaveService;
use App\Services\GiftCardService;
use App\Services\OrderService;
use App\Services\PaystackService;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaystackService $paystackService,
        private readonly FlutterwaveService $flutterwaveService,
        private readonly ReferralService $referralService,
        private readonly GiftCardService $giftCardService,
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

                    // Mark order as paid
                    $this->orderService->markOrderPaid($order, $reference);

                    // Send confirmation emails now that payment is verified
                    $this->dispatchOrderConfirmationEmails($order);

                    // Process referral + points (only once)
                    if (! $order->referral_processed) {
                        $this->referralService->processReferralForOrder($order, $request);
                        $this->referralService->processPointsRedemption($order);

                        $order->update([
                            'referral_processed' => true,
                        ]);
                    }

                    // Apply gift card redemption (if customer used one at checkout)
                    $this->giftCardService->applyToOrder($order);

                    // Issue gift card codes for any gift card products purchased
                    $this->giftCardService->issueForOrder($order);
                }

                return redirect()->route('checkout.success', [
                    'orderNumber' => $order?->order_number ?? $reference,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Paystack callback error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
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

                    // Mark order as paid
                    $this->orderService->markOrderPaid($order, $txRef);

                    // Send confirmation emails now that payment is verified
                    $this->dispatchOrderConfirmationEmails($order);

                    // Process referral + points (only once)
                    if (! $order->referral_processed) {
                        $this->referralService->processReferralForOrder($order, $request);
                        $this->referralService->processPointsRedemption($order);

                        $order->update([
                            'referral_processed' => true,
                        ]);
                    }

                    // Apply gift card redemption (if customer used one at checkout)
                    $this->giftCardService->applyToOrder($order);

                    // Issue gift card codes for any gift card products purchased
                    $this->giftCardService->issueForOrder($order);
                }

                return redirect()->route('checkout.success', [
                    'orderNumber' => $order?->order_number ?? $txRef,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Flutterwave callback error', [
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment could not be verified. Please contact support.');
    }

    private function dispatchOrderConfirmationEmails(Order $order): void
    {
        try {
            Mail::to($order->contact_email)->queue(new OrderConfirmation($order));

            if ((bool) AppSetting::get('notify_new_order', '1')) {
                $adminEmail = AppSetting::get('admin_notification_email', config('mail.from.address'));
                Mail::to($adminEmail)->later(now()->addSeconds(30), new AdminOrderNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Order confirmation email dispatch failed', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
