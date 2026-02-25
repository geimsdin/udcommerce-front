<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;

class FrontController extends ContentController
{
    protected function render(Request $request, array $params): mixed
    {
        abort(404);
    }
}
