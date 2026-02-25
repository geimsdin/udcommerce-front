<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;

class OrderConfirmationController extends ContentController
{
    protected array $slugVars = ['id'];

    protected function render(Request $request, array $params = []): mixed
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect('/address');
        }

        $this->setBreadcrumb();

        return view('front-ecommerce::front.order_confirmation', [
            'breadcrumb' => static::$breadcrumb,
        ]);
    }

    protected function setBreadcrumb(): void
    {
        $locale = app()->getLocale();

        static::$breadcrumb = [
            __('Home') => '/' . ($locale !== config('app.locale') ? $locale : ''),
            __('Order Confirmation') => '',
        ];
    }
}
