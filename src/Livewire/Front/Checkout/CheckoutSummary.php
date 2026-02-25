<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;

class CheckoutSummary extends Component
{
    public $product_data = [];

    public $totals;

    public $currency;

    protected $listeners = ['updateCart' => 'refreshTotals'];

    public function mount(): void
    {
        $this->refreshTotals();
    }

    public function refreshTotals(): void
    {
        $cart = Cart::getCurrentCart(true);
        if ($cart) {
            $this->totals = Cart::getTotalsForSummary($cart->id);
            $this->currency = Currency::find($cart->currency_id);
            $this->product_data = $cart->product_data ?? [];
        } else {
            $this->totals = null;
            $this->currency = null;
            $this->product_data = [];
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('front-ecommerce::livewire.front.checkout.checkout-summary', [
            'totals' => $this->totals,
            'currency' => $this->currency,
        ]);
    }
}
