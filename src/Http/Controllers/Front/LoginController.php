<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends ContentController
{
    public $login = false;

    protected function render(Request $request, array $params = []): mixed
    {
        // // If already logged in, redirect to account
        // // if (Auth::check()) {
        // if ($this->login) {
        //     return redirect('/account');
        // }

        $this->setBreadcrumb();

        return view('front-ecommerce::front.login', [
            'breadcrumb' => static::$breadcrumb,
        ]);
    }

    protected function setBreadcrumb(): void
    {
        static::$breadcrumb = [
            'Home' => '/',
            'Login' => '',
        ];
    }
}
