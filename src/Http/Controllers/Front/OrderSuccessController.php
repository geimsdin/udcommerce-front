<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;

class OrderSuccessController extends ContentController
{
    protected function render(Request $request, array $params = []): mixed
    {
        $this->setBreadcrumb();

        $orderId = session('checkout.last_order_id');
        $order = null;

        if ($orderId) {
            $order = \Unusualdope\LaravelEcommerce\Models\Order\Order::getOrderData($orderId);
        }

        $view = view('front-ecommerce::front.checkout.order-success', [
            'breadcrumb' => static::$breadcrumb,
            'order' => $order,
        ]);

        // Clear checkout session data after rendering the success page
        session()->forget('checkout');
        session()->forget('cart_id');

        return $view;
    }

    protected function setBreadcrumb(): void
    {
        static::$breadcrumb = [
            'Home' => '/',
            'Checkout' => '',
            'Ordine completato' => '',
        ];
    }
}
