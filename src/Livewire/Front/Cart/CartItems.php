<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Cart;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;

class CartItems extends Component
{
    public array $cart = [];

    public array $product_data = [];

    public $total_quantity = 0;

    public $currency;

    protected $listeners = ['updateCart' => 'updateCart'];

    public function mount(): void
    {
        $this->updateCart();
    }

    public function updateCart(): void
    {
        $cartModel = Cart::getCurrentCart();
        $this->product_data = $cartModel->product_data ?? [];
        $this->cart = $cartModel->only(['id', 'currency_id', 'carrier_id', 'client_id', 'note']);
        $totals = Cart::getTotalsForSummary($this->cart['id']);
        $this->total_quantity = $totals->total_quantity ?? 0;
        $this->currency = Currency::find($this->cart['currency_id']);
    }

    public function removeFromCart(int $product_id, int $variation_id): void
    {
        Cart::removeProductFromCart($product_id, $variation_id);
        $this->updateCart();
        $this->dispatch('updateCart');
    }

    public function increaseQuantity(int $product_id, int $variation_id): void
    {
        Cart::increaseQuantity($product_id, $variation_id);

        $this->updateCart();
        $this->dispatch('updateCart');
    }

    public function decreaseQuantity(int $product_id, int $variation_id): void
    {
        $key = $product_id.'_'.$variation_id;
        if (isset($this->product_data[$key]) && $this->product_data[$key]['total_quantity'] <= 1) {
            $this->removeFromCart($product_id, $variation_id);

            return;
        }

        Cart::decreaseQuantity($product_id, $variation_id);

        $this->updateCart();
        $this->dispatch('updateCart');
    }

    public function render(): \Illuminate\View\View
    {
        return view('front-ecommerce::livewire.front.cart.cart-items', [
            'product_data' => $this->product_data,
            'total_quantity' => $this->total_quantity,
            'currency' => $this->currency,
        ]);
    }
}
