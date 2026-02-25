# ud-front-laravel-ecommerce

Frontend package for Laravel ecommerce: FrontController, UrlMapper (CRUD), and URL-based controller registration.

## Dependency

- `unusualdope/laravel-ecommerce` (for Language model and `languages` table)

## Install

```bash
composer require unusualdope/front-laravel-ecommerce
```

## Config

Publish config (optional):

```bash
php artisan vendor:publish --tag=config
```

Or add to `config/ud-front-ecommerce.php`:

- `admin_route_prefix` – admin route prefix (Url Mapper CRUD). Set same as `ud-ecommerce.admin_route_prefix` to share admin menu.
- `admin_middleware` – admin route middleware.
- `front_route_prefix` – front URL prefix (catch-all), example: `shop` → `/shop/{path}`.
- `front_middleware` – front route middleware.
- `front_controllers` – additional controller array: `['Name' => FQCN::class]`.
- `language_model` – Language model FQCN (default: from ecommerce).

## Migration

```bash
php artisan migrate
```

Creates tables:
- `class_list` – stores controller list (front_controller, payment_gateway)
- `url_mapper` – friendly URL mapping per language to controller

## Usage

1. **Url Mapper (admin)**
   Go to `/{admin_route_prefix}/url-mapper` (e.g. `/admin/ecommerce/url-mapper`). Click "Scan Classes" to automatically discover controllers, then "Manage URLs" to set friendly URL per language.

2. **Front**
   Front URL: `/{front_route_prefix}/{path}` (e.g. `/shop/product`, `/shop/en/product`). First segment can be locale (iso_code) then friendly_url, or directly friendly_url with default language.

3. **Add front controller**
   - **Automatic**: Click "Scan Classes" button in Url Mapper page to automatically discover controllers.
   - **Via config**: Add to `config/ud-front-ecommerce.php`:
     ```php
     'front_controllers' => [
         'Category' => \App\Http\Controllers\Front\CategoryController::class,
     ],
     ```
   - **Via code**: In `AppServiceProvider::boot()`:
     ```php
     use Unusualdope\FrontLaravelEcommerce\Models\ClassList;
     ClassList::register('Category', \App\Http\Controllers\Front\CategoryController::class, 'front_controller');
     ```

   Controller must have method: `handle(Request $request, string $slug = '')`.
