<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends ContentController
{
    protected function render(Request $request, array $params = []): mixed
    {
        if (!Auth::check()) {
            return redirect('/login?redirect=' . urlencode($request->fullUrl()));
        }

        $this->setBreadcrumb();

        return view('front-ecommerce::front.checkout.shipping', [
            'breadcrumb' => static::$breadcrumb,
        ]);
    }

    protected function setBreadcrumb(): void
    {
        static::$breadcrumb = [
            'Home' => '/',
            'Checkout' => '/checkout',
            'Metodo di spedizione' => '',
        ];
    }
}
