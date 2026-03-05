<?php

namespace Unusualdope\FrontLaravelEcommerce;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Auth\LoginForm;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Auth\RegisterForm;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout\AddressForm;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout\ShippingForm;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout\PaymentForm;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout\CheckoutSummary;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Catalog\ProductCatalog;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Catalog\ProductMiniature;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home\BrandCarousel;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home\DiscoverWeekly;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home\PopularSearches;
use Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home\TopBrands;
use Unusualdope\FrontLaravelEcommerce\Models\ClassList;
use Unusualdope\FrontLaravelEcommerce\Providers\ViewServiceProvider;

class UdFrontLaravelEcommerceServiceProvider extends ServiceProvider
{
    /**
     * All of the package service providers.
     *
     * @var array<int, class-string>
     */
    protected array $providers = [
        ViewServiceProvider::class,
    ];

    /**
     * @var array<int, class-string>
     */
    protected array $gateways = [
        \Unusualdope\LaravelEcommerce\Payment\Gateways\PaypalPaymentGateway::class,
    ];

    public function boot(): void
    {
        $this->mergeTypesenseScoutConfig();

        $this->registerPaymentGateways();

        // $this->registerFrontControllers();
        $this->registerLivewireComponents();

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'front-ecommerce');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load admin views (these are not meant to be published/customized)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'front-ecommerce');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ud-front-ecommerce.php' => config_path('ud-front-ecommerce.php'),
            ], 'config');

            // Publish assets (CSS, JS, images)
            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('vendor/ud-front-ecommerce'),
            ], 'ud-front-ecommerce-assets');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ud-front-ecommerce.php', 'ud-front-ecommerce');

        $this->app->singleton(\Unusualdope\LaravelEcommerce\Payment\PaymentGatewayManager::class, function ($app) {
            return new \Unusualdope\LaravelEcommerce\Payment\PaymentGatewayManager();
        });

        // Register package service providers
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Merge package Typesense config into Laravel Scout so SCOUT_DRIVER=typesense works.
     */
    protected function mergeTypesenseScoutConfig(): void
    {
        $packageTypesense = require __DIR__ . '/../config/scout_typesense.php';
        $existing = config('scout.typesense', []);
        config(['scout.typesense' => array_replace_recursive($packageTypesense, $existing)]);
    }

    protected function registerFrontControllers(): void
    {
        // Register controllers from config file to database (ClassList)
        try {
            $custom = config('ud-front-ecommerce.front_controllers', []);
            foreach ($custom as $name => $fqcn) {
                ClassList::register($name, $fqcn, 'front_controller');
            }
        } catch (\Exception $e) {
            if (config('app.debug')) {
                logger()->warning('[FrontEcommerce] Could not auto-register controllers: ' . $e->getMessage());
            }
        }
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('front-ecommerce.catalog.product-catalog', ProductCatalog::class);
        Livewire::component('front-ecommerce.catalog.product-miniature', ProductMiniature::class);
        Livewire::component('front-ecommerce.home.discover-weekly', DiscoverWeekly::class);
        Livewire::component('front-ecommerce.home.top-brands', TopBrands::class);
        Livewire::component('front-ecommerce.home.popular-searches', PopularSearches::class);
        Livewire::component('front-ecommerce.home.brand-carousel', BrandCarousel::class);
        Livewire::component('front-ecommerce.auth.login-form', LoginForm::class);
        Livewire::component('front-ecommerce.auth.register-form', RegisterForm::class);
        Livewire::component('front-ecommerce.checkout.address-form', AddressForm::class);
        Livewire::component('front-ecommerce.checkout.shipping-form', ShippingForm::class);
        Livewire::component('front-ecommerce.checkout.payment-form', PaymentForm::class);
        Livewire::component('front-ecommerce.checkout.checkout-summary', CheckoutSummary::class);
        Livewire::component('front-ecommerce.cart.cart-items', \Unusualdope\FrontLaravelEcommerce\Livewire\Front\Cart\CartItems::class);
        Livewire::component('front-ecommerce.cart.cart-summary', \Unusualdope\FrontLaravelEcommerce\Livewire\Front\Cart\CartSummary::class);
        Livewire::component('front-ecommerce.fly-cart.fly-cart', \Unusualdope\FrontLaravelEcommerce\Livewire\Front\FlyCart\FlyCart::class);
        Livewire::component('front-ecommerce.fly-cart.fly-cart-line', \Unusualdope\FrontLaravelEcommerce\Livewire\Front\FlyCart\FlyCartLine::class);
        Livewire::component('front-ecommerce.currency-switcher', \Unusualdope\FrontLaravelEcommerce\Livewire\Front\CurrencySwitcher::class);

    }

    protected function registerPaymentGateways(): void
    {
        if ($this->app->runningInConsole() || !Schema::hasTable('payment_gateways')) {
            return;
        }

        try {
            $this->app->make(\Unusualdope\LaravelEcommerce\Payment\PaymentGatewayManager::class)->syncGateways();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                logger()->warning('[FrontEcommerce] Could not sync payment gateways: ' . $e->getMessage());
            }
        }
    }
}
