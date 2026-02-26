<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Payment\PaymentGatewayManager;

class PaymentForm extends Component
{
    public $selectedMethod = null;

    public $termsAccepted = false;

    public function mount(PaymentGatewayManager $gatewayManager)
    {
        $gateways = $gatewayManager->getActiveGateways();

        // Default to contrassegno as requested
        $this->selectedMethod = 'contrassegno';

        // If contrassegno is not available, fallback to the first gateway
        if (!$gateways->contains('slug', $this->selectedMethod) && $gateways->isNotEmpty()) {
            $this->selectedMethod = $gateways->first()->getSlug();
        }

        // Initialize session if not set to ensure summary shows default COD fee
        $this->updatedSelectedMethod($this->selectedMethod);
    }

    public function updatedSelectedMethod($value)
    {
        $gatewayManager = app(PaymentGatewayManager::class);
        $gateway = $gatewayManager->getGatewayBySlug($value);

        if ($gateway) {
            $paymentDetails = [
                'slug' => $value,
                'name' => $gateway::getName(),
            ];

            if ($gateway instanceof \Unusualdope\LaravelEcommerce\Payment\Gateways\CashOnDeliveryPaymentGateway) {
                $paymentDetails['fee'] = $gateway->getFee();
            }

            session()->put('checkout.payment_method', $paymentDetails);
            session()->save();
            $this->dispatch('updateCart');
        }
    }

    public function submit()
    {
        if (!$this->termsAccepted) {
            $this->addError('termsAccepted', 'Devi accettare i termini del servizio per continuare.');

            return;
        }

        if (!$this->selectedMethod) {
            $this->addError('selectedMethod', 'Seleziona un metodo di pagamento.');

            return;
        }

        $gatewayManager = app(PaymentGatewayManager::class);
        $gateway = $gatewayManager->getGatewayBySlug($this->selectedMethod);

        if (!$gateway) {
            $this->addError('selectedMethod', 'Metodo di pagamento non valido.');

            return;
        }

        // Finalize session data
        $paymentDetails = [
            'slug' => $this->selectedMethod,
            'name' => $gateway::getName(),
        ];

        if ($gateway instanceof \Unusualdope\LaravelEcommerce\Payment\Gateways\BankWirePaymentGateway) {
            $paymentDetails['bank_details'] = $gateway->getBankDetails();
            $paymentDetails['finish_message'] = $gateway->getFinishMessage();
        }

        if ($gateway instanceof \Unusualdope\LaravelEcommerce\Payment\Gateways\CashOnDeliveryPaymentGateway) {
            $paymentDetails['fee'] = $gateway->getFee();
        }

        session()->put('checkout.payment_method', $paymentDetails);
        session()->save();

        // Create the Order
        try {
            $order = \Unusualdope\LaravelEcommerce\Models\Order\Order::createFromFrontSession();
        } catch (\Exception $e) {
            $this->addError('selectedMethod', 'Errore durante la creazione dell\'ordine: ' . $e->getMessage());

            return;
        }

        if (!$order) {
            $missingKeys = [];
            if (!session('checkout.shipping_method')) {
                $missingKeys[] = 'shipping_method';
            }
            if (!session('checkout.payment_method')) {
                $missingKeys[] = 'payment_method';
            }
            if (!session('checkout.shipping_address_id')) {
                $missingKeys[] = 'shipping_address_id';
            }
            if (!session('checkout.billing_address_id')) {
                $missingKeys[] = 'billing_address_id';
            }

            if (!empty($missingKeys)) {
                $errorMsg = 'Si è verificato un errore durante la creazione dell\'ordine. Dati mancanti: ' . implode(', ', $missingKeys);
            } else {
                $errorMsg = 'Si è verificato un errore durante la creazione dell\'ordine. Riprova o contatta il supporto.';
            }

            $this->addError('selectedMethod', $errorMsg);

            return;
        }

        session()->put('checkout.last_order_id', $order->id);

        // Handle Payment Redirect or Success
        if ($gateway->type === 'offline') {
            return redirect('/order-success');
        }

        // Omnipay handling (e.g. PayPal)
        try {
            $paymentService = app(\Unusualdope\FrontLaravelEcommerce\Services\PaymentService::class);
            $orderData = \Unusualdope\LaravelEcommerce\Models\Order\Order::getOrderData($order->id);

            $purchaseData = [
                'amount' => $orderData->grand_total,
                'order_id' => $order->id,
                'currency' => $orderData->currency->iso_code ?? 'EUR',
            ];

            $response = $paymentService->initiatePurchase($this->selectedMethod, $purchaseData);

            if ($response->isRedirect()) {
                return redirect()->away($response->getRedirectUrl());
            }

            if ($response->isSuccessful()) {
                return redirect('/order-success');
            }

            $this->addError('selectedMethod', 'Errore gateway: ' . $response->getMessage());

        } catch (\Exception $e) {
            $this->addError('selectedMethod', 'Si è verificato un errore con il gateway di pagamento.');
        }
    }

    public function render(PaymentGatewayManager $gatewayManager)
    {
        return view('front-ecommerce::livewire.front.checkout.payment-form', [
            'gateways' => $gatewayManager->getActiveGateways(),
        ]);
    }
}
