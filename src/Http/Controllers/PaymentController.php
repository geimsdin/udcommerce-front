<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers;

use Illuminate\Http\Request;
use Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front\FrontController;
use Unusualdope\FrontLaravelEcommerce\Services\PaymentService;

class PaymentController extends FrontController
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * Handle checkout payment submission.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'order_id' => 'nullable|string',
            'stripeToken' => 'nullable|string',
        ]);

        try {
            $purchaseData = [
                'amount' => $request->amount,
                'order_id' => $request->order_id ?? null,
                'currency' => $request->currency ?? 'USD',
            ];

            if ($request->filled('stripeToken')) {

                $purchaseData['token'] = $request->stripeToken;
            }

            $response = $this->paymentService->initiatePurchase($request->payment_method, $purchaseData);

            if ($response->isRedirect()) {
                return $response->redirect();
            }

            if ($response->isSuccessful()) {
                return back()->with('success', 'Payment successful! Transaction Ref: '.$response->getTransactionReference());
            }

            return back()->with('error', $response->getMessage());

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle successful payment callback.
     */
    public function success(Request $request, string $gateway, int $payment_id)
    {
        try {
            $response = $this->paymentService->completePurchase($gateway, $request, $payment_id);

            if ($response->isSuccessful()) {
                // Here we could redirect to a success page or order confirmation
                return redirect('/order-success')
                    ->with('success', 'Payment successful!');
            }

            return redirect('/checkout')
                ->with('error', 'Payment failed: '.$response->getMessage());

        } catch (\Exception $e) {
            return redirect('/checkout')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Handle cancelled payment callback.
     */
    public function cancel(Request $request, string $gateway, int $payment_id)
    {
        // Update payment status if needed
        return redirect('/checkout')
            ->with('info', 'Payment was cancelled.');
    }
}
