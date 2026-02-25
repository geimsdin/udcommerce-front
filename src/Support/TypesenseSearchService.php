<?php

namespace Unusualdope\FrontLaravelEcommerce\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Scout\Builder;
use Typesense\Exceptions\ObjectNotFound;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;

class TypesenseSearchService
{
    /**
     * Find a product by parameters.
     */
    public function findProduct(array $params): ?Product
    {
        $productModel = Config::get('ud-front-ecommerce.product_model', Product::class);
        $searchQuery = '';

        if (isset($params['slug']) || isset($params['link_rewrite'])) {
            $searchQuery = $params['slug'] ?? $params['link_rewrite'];
        }

        $builder = $productModel::search($searchQuery ?: '*');

        if (isset($params['id'])) {
            $builder->where('id', (int) $params['id']);
        }

        if (isset($params['sku'])) {
            $builder->where('sku', $params['sku']);
        }

        // Filter by active status
        $builder->where('status', 1);

        return $builder->first();
    }

    /**
     * Search products with filters.
     */
    public function searchProducts(
        ?int $categoryId = null,
        ?int $brandId = null,
        int $limit = 16,
        int $offset = 0,
        string $orderBy = 'created_at',
        string $orderWay = 'desc'
    ): array {
        $productModel = Config::get('ud-front-ecommerce.product_model', Product::class);

        $builder = $productModel::search('*');

        if ($categoryId) {
            $builder->where('category_ids', $categoryId);
        }

        if ($brandId) {
            $builder->where('brand_id', $brandId);
        }

        $builder->where('status', 1);

        $limit = max(1, (int) $limit);
        $offset = max(0, (int) $offset);
        $page = intdiv($offset, $limit) + 1;

        try {
            $builder->orderBy($orderBy, $orderWay);
            $paginator = $builder->paginate($limit, 'page', $page);
        } catch (ObjectNotFound $e) {
            // Schema may not have the requested sort field (e.g. language_name, name). Fall back to created_at.
            if (str_contains($e->getMessage(), 'sorting') || str_contains($e->getMessage(), 'field named')) {
                $builder = $productModel::search('*');
                if ($categoryId) {
                    $builder->where('category_ids', $categoryId);
                }
                if ($brandId) {
                    $builder->where('brand_id', $brandId);
                }
                $builder->where('status', 1);
                $builder->orderBy('created_at', 'desc');
                $paginator = $builder->paginate($limit, 'page', $page);
            } else {
                throw $e;
            }
        }

        $products = $paginator->getCollection();

        // Load necessary relations for views
        if ($products->isNotEmpty()) {
            $products->load(['images', 'currentLanguage', 'brand']);
            $paginator->setCollection($products);
        }

        return [
            'products' => $paginator,
            'total' => (int) $paginator->total(),
        ];
    }

    /**
     * Find a category by parameters.
     */
    public function findCategory(array $params): ?ProductCategory
    {
        $categoryModel = Config::get('ud-front-ecommerce.category_model', ProductCategory::class);
        $searchQuery = '';

        if (isset($params['category']) || isset($params['slug']) || isset($params['link_rewrite'])) {
            $searchQuery = $params['category'] ?? $params['slug'] ?? $params['link_rewrite'];
        }

        $builder = $categoryModel::search($searchQuery ?: '*');

        if (isset($params['id'])) {
            $builder->where('id', (int) $params['id']);
        }

        $builder->where('status', 1);

        return $builder->first();
    }

    /**
     * Find a brand by parameters.
     */
    public function findBrand(array $params): ?Brand
    {
        $brandModel = Config::get('ud-front-ecommerce.brand_model', Brand::class);
        $searchQuery = '';

        if (isset($params['brand']) || isset($params['slug'])) {
            $searchQuery = $params['brand'] ?? $params['slug'];
        }

        $builder = $brandModel::search($searchQuery ?: '*');

        if (isset($params['id'])) {
            $builder->where('id', (int) $params['id']);
        }

        $builder->where('status', 1);

        return $builder->first();
    }

    public function getTopBrands(int $limit = 20): Collection
    {
        $brandModel = Config::get('ud-front-ecommerce.brand_model', Brand::class);

        return $brandModel::search('*')
            ->query(fn($q) => $q->withCount('products'))
            ->orderBy('products_count', 'desc')
            ->take($limit * 2) // Fetch more to allow for PHP-side filtering
            ->get()
            ->filter(fn($brand) => $brand->products_count > 0)
            ->take($limit);
    }

    /**
     * Get popular categories (from Typesense).
     */
    public function getPopularCategories(int $limit = 36): Collection
    {
        $categoryModel = Config::get('ud-front-ecommerce.category_model', ProductCategory::class);

        return $categoryModel::search('*')
            ->query(fn($q) => $q->withCount('products'))
            ->where('status', 1)
            ->orderBy('products_count', 'desc')
            ->take($limit * 2) // Fetch more to allow for PHP-side filtering
            ->get()
            ->filter(fn($category) => $category->products_count > 0)
            ->take($limit);
    }
}
