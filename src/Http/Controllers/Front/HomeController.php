<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;

class HomeController extends ContentController
{
    protected array $slugVars = ['id'];

    protected function render(Request $request, array $params = []): mixed
    {
        return view('front-ecommerce::front.home');
    }
}
