<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;

class CartController extends ContentController
{
    protected array $slugVars = ['id'];

    protected function render(Request $request, array $params = []): mixed
    {
        $this->setBreadcrumb();

        return view('front-ecommerce::front.cart', [
            'breadcrumb' => static::$breadcrumb,
        ]);
    }

    protected function setBreadcrumb(): void
    {
        $locale = app()->getLocale();

        static::$breadcrumb = [
            __('Home') => '/'.($locale !== config('app.locale') ? $locale : ''),
            __('Cart') => '',
        ];
    }
}
