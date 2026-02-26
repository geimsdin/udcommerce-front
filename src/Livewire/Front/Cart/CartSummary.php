<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Cart;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;
use Unusualdope\LaravelEcommerce\Models\Coupon;

class CartSummary extends Component
{
    public $totals;

    public $currency;

    public string $couponCode = '';

    public ?array $appliedCoupon = null;

    public ?string $couponError = null;

    public float $discountAmount = 0.0;

    protected $listeners = ['updateCart' => 'refreshTotals'];

    public function mount(): void
    {
        $this->refreshTotals();

        // Rehydrate from the persisted coupon_id on the cart record
        if (! $this->appliedCoupon) {
            $cart = Cart::getCurrentCart(false);
            $coupon = $cart ? Coupon::getFromCart($cart) : null;

            if ($coupon) {
                $this->hydrateFromCoupon($coupon);
            }
        }
    }

    public function refreshTotals(): void
    {
        $cart = Cart::getCurrentCart(false);
        $this->totals = Cart::getTotalsForSummary();
        $this->currency = Currency::find($cart->currency_id);

        // Recalculate discount whenever totals change
        if ($this->appliedCoupon) {
            $coupon = Coupon::find($this->appliedCoupon['id']);

            if ($coupon && $coupon->isActive()) {
                $this->discountAmount = $coupon->calculateDiscount($this->totals->grand_total ?? 0);
            } else {
                $this->appliedCoupon = null;
                $this->discountAmount = 0.0;
            }
        }
    }

    public function applyCoupon(): void
    {
        $this->couponError = null;
        $code = trim($this->couponCode);

        if (empty($code)) {
            $this->couponError = __('front-ecommerce::cart.coupon_empty');

            return;
        }

        $coupon = Coupon::findByCode($code);

        if (! $coupon) {
            $this->couponError = __('front-ecommerce::cart.coupon_invalid');

            return;
        }

        if (! $coupon->isActive()) {
            $this->couponError = __('front-ecommerce::cart.coupon_expired');

            return;
        }

        $cart = Cart::getCurrentCart();
        $error = $coupon->validateForCart($cart, $this->totals);

        if ($error) {
            $this->couponError = $error;

            return;
        }

        $coupon->applyToCart($cart);

        $this->hydrateFromCoupon($coupon);
    }

    public function removeCoupon(): void
    {
        $cart = Cart::getCurrentCart();
        if ($cart) {
            Coupon::removeFromCart($cart);
        }

        $this->appliedCoupon = null;
        $this->couponCode = '';
        $this->couponError = null;
        $this->discountAmount = 0.0;
    }

    public function render(): \Illuminate\View\View
    {
        return view('front-ecommerce::livewire.front.cart.cart-summary', [
            'totals' => $this->totals,
            'currency' => $this->currency,
            'appliedCoupon' => $this->appliedCoupon,
            'couponError' => $this->couponError,
            'discountAmount' => $this->discountAmount,
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function hydrateFromCoupon(Coupon $coupon): void
    {
        $this->appliedCoupon = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
        ];
        $this->discountAmount = $coupon->calculateDiscount($this->totals->grand_total ?? 0);
    }
}
