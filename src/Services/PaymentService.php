<?php

namespace Unusualdope\FrontLaravelEcommerce\Services;

use Illuminate\Http\Request;
use Unusualdope\LaravelEcommerce\Payment\PaymentGatewayManager;
use Unusualdope\LaravelEcommerce\Models\Payment\Payment;

class PaymentService
{
    public function __construct(protected PaymentGatewayManager $gatewayManager)
    {
    }

    /**
     * Initiate a purchase.
     */
    public function initiatePurchase(string $gatewaySlug, array $params)
    {
        $gateway = $this->gatewayManager->getGatewayBySlug($gatewaySlug);

        if (!$gateway) {
            throw new \Exception("Payment gateway [{$gatewaySlug}] not found or inactive.");
        }

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'order_id' => $params['order_id'] ?? null,
            'gateway_slug' => $gatewaySlug,
            'amount' => $params['amount'],
            'status' => 'pending',
            'idempotency_key' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $transactionId = $params['order_id'] ?? (string) \Illuminate\Support\Str::uuid();

        $payment->update([
            'transaction_id' => $transactionId,
        ]);

        $purchasePayload = [
            'amount' => number_format($params['amount'], 2, '.', ''),
            'currency' => $params['currency'] ?? 'USD',
            'description' => 'Order #' . ($params['order_id'] ?? $transactionId),
            'returnUrl' => route('payment.success', ['gateway' => $gatewaySlug, 'payment_id' => $payment->id]),
            'cancelUrl' => route('payment.cancel', ['gateway' => $gatewaySlug, 'payment_id' => $payment->id]),
            'transactionId' => $transactionId,
            'idempotencyKey' => $payment->idempotency_key,
        ];

        if (isset($params['token'])) {
            // Pass the Stripe.js token to the gateway
            $purchasePayload['token'] = $params['token'];
        }

        $response = $gateway->purchase($purchasePayload);

        if ($response->isRedirect()) {
            $payment->update([
                'transaction_id' => $response->getTransactionReference(), // Some providers give ref here
            ]);
        } elseif ($response->isSuccessful()) {
            $payment->update([
                'status' => 'completed',
                'gateway_reference' => $response->getTransactionReference(),
                'completed_at' => now(),
            ]);
        }

        return $response;
    }

    /**
     * Complete a purchase.
     */
    public function completePurchase(string $gatewaySlug, Request $request, int $paymentId)
    {
        $gateway = $this->gatewayManager->getGatewayBySlug($gatewaySlug);
        $payment = Payment::findOrFail($paymentId);

        if (!$gateway) {
            throw new \Exception("Payment gateway [{$gatewaySlug}] not found.");
        }

        $response = $gateway->completePurchase([
            'transactionReference' => $request->input('paymentId') ?? $request->input('transactionId') ?? $payment->transaction_id,
            'payer_id' => $request->input('PayerID'),
        ]);

        if ($response->isSuccessful()) {
            $payment->update([
                'status' => 'completed',
                'gateway_reference' => $response->getTransactionReference(),
                'completed_at' => now(),
            ]);
        } else {
            $payment->update([
                'status' => 'failed',
            ]);
        }

        return $response;
    }

    /**
     * Handle incoming payment gateway webhook notification.
     */
    public function handleWebhook(string $gatewaySlug, Request $request)
    {
        $gateway = $this->gatewayManager->getGatewayBySlug($gatewaySlug);

        if (!$gateway) {
            throw new \Exception("Payment gateway [{$gatewaySlug}] not found.");
        }

        // Pass the full request for signature verification
        $notification = $gateway->acceptNotification($request);

        // Find by gateway reference OR transaction ID
        $transactionReference = $notification->getTransactionReference();

        $payment = Payment::where('gateway_reference', $transactionReference)
            ->orWhere('transaction_id', $transactionReference)
            ->first();

        if ($payment instanceof Payment) {
            if (in_array($payment->status, ['completed', 'failed'])) {
                return $notification;
            }

            $status = $notification->getTransactionStatus();

            if ($status === \Omnipay\Common\Message\NotificationInterface::STATUS_COMPLETED) {
                $payment->status = 'completed';
                $payment->completed_at = now();
                $payment->save();
            } elseif ($status === \Omnipay\Common\Message\NotificationInterface::STATUS_FAILED) {
                $payment->status = 'failed';
                $payment->save();
            }
        }

        return $notification;
    }
}
