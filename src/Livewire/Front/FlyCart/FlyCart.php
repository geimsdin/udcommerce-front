<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\FlyCart;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;

class FlyCart extends Component
{
    public $cart = [];

    public $product_data = [];

    public $totals;

    public $total_quantity;

    public $currency;

    public $isOpen = false;

    protected $listeners = ['updateCart' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        $cartModel = Cart::getCurrentCart(); // loads details & builds product_data
        $this->product_data = $cartModel->product_data ?? [];
        $this->cart = $cartModel->only(['id', 'currency_id', 'carrier_id', 'client_id', 'note']);
        $this->totals = Cart::getTotalsForSummary($this->cart['id']);
        $this->total_quantity = $this->totals->total_quantity;
        $this->currency = Currency::find($this->cart['currency_id']);
    }

    public function openCart()
    {
        $this->isOpen = true;
    }

    public function closeCart()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('front-ecommerce::livewire.front.fly-cart.fly-cart', [
            'cart' => $this->cart,
            'product_data' => $this->product_data,
            'total_quantity' => $this->total_quantity,
            'totals' => $this->totals,
            'currency' => $this->currency,
        ]);
    }
}
