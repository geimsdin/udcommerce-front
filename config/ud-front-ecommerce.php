<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin route prefix (for Url Mapper CRUD)
    |--------------------------------------------------------------------------
    | Set same as ud-ecommerce.admin_route_prefix so Url Mapper appears in the same admin menu.
    */
    'admin_route_prefix' => 'admin.ecommerce',

    'admin_middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Front Controllers (optional)
    |--------------------------------------------------------------------------
    | Add front controller: ['Display Name' => FQCN::class]
    */
    'front_controllers' => [],

    'front_middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Language model (from ecommerce or app)
    |--------------------------------------------------------------------------
    */
    'language_model' => \Unusualdope\LaravelEcommerce\Models\Language::class,

    /*
    |--------------------------------------------------------------------------
    | Product Model (from app or package)
    |--------------------------------------------------------------------------
    | Fully qualified class name of your Product model.
    | This will be used by ProductController to resolve products.
    */
    'product_model' => \Unusualdope\LaravelEcommerce\Models\Product\Product::class, // Example: \App\Models\Product::class

    /*
    |--------------------------------------------------------------------------
    | Category Model (from app or package)
    |--------------------------------------------------------------------------
    | Fully qualified class name of your ProductCategory model.
    | This will be used by ProductListingController to resolve categories.
    */
    'category_model' => \Unusualdope\LaravelEcommerce\Models\Product\ProductCategory::class,

    /*
    |--------------------------------------------------------------------------
    | Brand Model (from app or package)
    |--------------------------------------------------------------------------
    | Fully qualified class name of your Brand model.
    | This will be used by ProductListingController to resolve brands.
    */
    'brand_model' => \Unusualdope\LaravelEcommerce\Models\Product\Brand::class,

    /*
    |--------------------------------------------------------------------------
    | URL Patterns (PrestaShop-style flexible URLs)
    |--------------------------------------------------------------------------
    | Define URL patterns for each controller. Patterns use placeholders
    | wrapped in curly braces {} that will be extracted from the URL.
    |
    | Available placeholders depend on the controller's $slugVars property.
    |
    | Example patterns:
    | - ProductController: '{id}-{slug}' -> /product/123-my-product
    | - ProductController: '{slug}' -> /product/my-product
    | - ProductController: '{category}/{slug}' -> /product/electronics/my-product
    | - ProductListingController: '{category}' -> /category/shoes
    | - ProductListingController: 'brand/{brand}' -> /brand/nike
    |
    | Leave empty to use legacy slug-based routing.
    */
    'url_patterns' => [
        // \Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front\ProductController::class => '{id}-{slug}',
        // \Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front\ProductListingController::class => '{category}',
    ],
];
