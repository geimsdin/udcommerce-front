<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends ContentController
{
    protected function render(Request $request, array $params = []): mixed
    {
        // Require authentication
        if (! Auth::check()) {
            return redirect('/login');
        }

        $this->setBreadcrumb();

        return view('front-ecommerce::front.account', [
            'breadcrumb' => static::$breadcrumb,
            'user' => Auth::user(),
        ]);
    }

    protected function setBreadcrumb(): void
    {
        static::$breadcrumb = [
            'Home' => '/',
            'Account' => '',
        ];
    }
}
