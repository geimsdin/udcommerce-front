<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use Unusualdope\FrontLaravelEcommerce\Support\TypesenseSearchService;

class ProductController extends ContentController
{
    public function __construct(protected TypesenseSearchService $searchService)
    {
    }

    protected array $slugVars = [
        'id',
        'link_rewrite',
        'sku',
        'brand',
        'category',
    ];

    protected function render(Request $request, array $params): View
    {
        $product = $this->findProduct($params);
        if (! $product && isset($params['id'])) {
            $product = $this->findProductByIdFromDatabase((int) $params['id']);
        }

        if (! $product) {
            abort(404, 'Product not found.');
        }

        $this->setBreadcrumb($product, $params);

        // dd($product, $params);
        return view('front-ecommerce::front.product', [
            'product' => $product,
            'params' => $params,
            'breadcrumb' => static::$breadcrumb,
        ]);
    }

    protected function setBreadcrumb($product, array $params): void
    {
        $locale = app()->getLocale();

        // Get product name safely (handle translations)
        $productName = 'Product';
        if ($product) {
            if (is_string($product->name)) {
                $productName = $product->name;
            } elseif (isset($product->title) && is_string($product->title)) {
                $productName = $product->title;
            }
        }

        static::$breadcrumb = [
            'Home' => '/',
            'Products' => '/product',
            $productName => '',
        ];
    }

    /**
     * Find product from URL params.
     */
    protected function findProduct(array $params): mixed
    {
        return $this->searchService->findProduct($params);
    }

    protected function getProductModel(): ?object
    {
        return Config::get('ud-front-ecommerce.product_model') ? app(Config::get('ud-front-ecommerce.product_model')) : null;
    }

    public function handleWithSlug(Request $request, string $slug = ''): mixed
    {
        // Parse slug using URL pattern (e.g., "1-example-product" -> id=1, slug=example-product)
        $urlPattern = $this->getUrlPattern();
        $params = $this->parseUrlFromPattern($urlPattern, $slug);

        // If pattern matched and we have id or other specific params, use them
        if (!empty($params) && (isset($params['id']) || (count($params) > 1 && !isset($params['slug'])))) {
            return $this->render($request, $params);
        }

        // If we have a slug/link_rewrite in params, try to find product
        if (isset($params['slug']) || isset($params['link_rewrite'])) {
            $linkRewrite = $params['slug'] ?? $params['link_rewrite'];
            $product = $this->findProduct(['link_rewrite' => $linkRewrite]);
            // dd($product);
            if ($product) {
                return $this->render($request, ['link_rewrite' => $linkRewrite, 'id' => $product->id]);
            }
        }

        // Fallback: try to find by link_rewrite directly using the slug
        $productModel = $this->getProductModel();
        $languageId = $this->getCurrentLanguageId();

        if ($productModel && !empty($slug)) {
            $query = $productModel::query();

            if ($languageId) {
                $product = $query->whereHas('languages', function ($q) use ($slug, $languageId) {
                    $q->where('link_rewrite', $slug)
                        ->where('language_id', $languageId);
                })->first();
            } else {
                $product = $query->whereHas('languages', fn($q) => $q->where('link_rewrite', $slug))->first();
            }

            if ($product) {
                return $this->render($request, ['link_rewrite' => $slug, 'id' => $product->id]);
            }
        }

        abort(404, 'Product not found.');
    }
    /**
     * Fallback: load product by ID from database when Scout/Typesense returns null.
     */
    protected function findProductByIdFromDatabase(int $id): ?object
    {
        $productModel = $this->getProductModel();
        if (! $productModel) {
            return null;
        }

        $product = $productModel::query()->where('id', $id)->where('status', 1)->first();

        return $product ?: null;
    }
}
