<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\FlyCart;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;

class FlyCartLine extends Component
{
    public array $product;
    public int $cart_id;
    public $currency;

    public function removeFromCart(): void
    {
        Cart::removeProductFromCart($this->product['product_id'], $this->product['variation_id']);
        $this->dispatch('updateCart');
    }

    public function increaseQuantity(): void
    {
        Cart::increaseQuantity($this->product['product_id'], $this->product['variation_id']);

        $this->product['total_quantity']++;
        $this->product['total_price'] = $this->product['total_quantity'] * $this->product['product_price'];

        $this->dispatch('updateCart');
    }

    public function decreaseQuantity(): void
    {
        if ($this->product['total_quantity'] <= 1) {
            $this->removeFromCart();
            return;
        }

        Cart::decreaseQuantity($this->product['product_id'], $this->product['variation_id']);

        $this->product['total_quantity']--;
        $this->product['total_price'] = $this->product['total_quantity'] * $this->product['product_price'];

        $this->dispatch('updateCart');
    }

    public function render()
    {
        return view('front-ecommerce::livewire.front.fly-cart.fly-cart-line');
    }
}
