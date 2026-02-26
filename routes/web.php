<?php

use Illuminate\Support\Facades\Route;
use Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front\FrontController;
use Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front\SocialAuthController;
use Unusualdope\FrontLaravelEcommerce\Livewire\Admin\UrlMapper\UrlMapperEdit;
use Unusualdope\FrontLaravelEcommerce\Livewire\Admin\UrlMapper\UrlMapperIndex;

// Admin: URL Mapper (under same prefix as ecommerce admin)
$name = config('ud-front-ecommerce.admin_route_prefix');
$prefix = str_replace('.', '/', $name);
$middleware = config('ud-front-ecommerce.admin_middleware', ['web', 'auth']);
Route::prefix($prefix)
    ->middleware($middleware)
    ->name($name . '.')
    ->group(function () {
        Route::livewire('url-mapper', UrlMapperIndex::class)->name('url-mapper.index');
        Route::livewire('url-mapper/edit/{controllerName}', UrlMapperEdit::class)->name('url-mapper.edit');
    });

Route::middleware(config('ud-front-ecommerce.front_middleware', ['web']))
    ->group(function () {
        Route::get('auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
            ->name('social.redirect');
        Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])
            ->name('social.callback');
        Route::post('payment/checkout', [\Unusualdope\FrontLaravelEcommerce\Http\Controllers\PaymentController::class, 'checkout'])
            ->name('payment.checkout');
        Route::get('payment/{gateway}/success/{payment_id}', [\Unusualdope\FrontLaravelEcommerce\Http\Controllers\PaymentController::class, 'success'])
            ->name('payment.success');
        Route::get('payment/{gateway}/cancel/{payment_id}', [\Unusualdope\FrontLaravelEcommerce\Http\Controllers\PaymentController::class, 'cancel'])
            ->name('payment.cancel');
        Route::post('payment/webhook/{gateway}', [\Unusualdope\FrontLaravelEcommerce\Http\Controllers\PaymentController::class, 'webhook'])
            ->name('payment.webhook');
    });

Route::middleware(config('ud-front-ecommerce.front_middleware', ['web']))
    ->group(function () {
        Route::fallback([FrontController::class, 'handle'])
            ->name('ecommerce.front');
    });
