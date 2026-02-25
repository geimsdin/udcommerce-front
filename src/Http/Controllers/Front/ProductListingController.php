<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

use Unusualdope\FrontLaravelEcommerce\Support\TypesenseSearchService;

class ProductListingController extends ContentController
{
    public function __construct(protected TypesenseSearchService $searchService)
    {
    }

    protected array $slugVars = [
        'id',
        'link_rewrite',
        'category',
        'brand',
    ];

    /**
     * Handle product listing request.
     * Can filter by category and/or brand.
     */
    protected function render(Request $request, array $params): View
    {
        $category = null;
        $brand = null;

        // Find category from URL params
        if (!empty($params['category']) || !empty($params['id'])) {
            $category = $this->findCategory($params);
            if (!$category) {
                abort(404, 'Category not found.');
            }
        }

        // Find brand from URL params
        if (!empty($params['brand'])) {
            $brand = $this->findBrand($params);
            if (!$brand) {
                abort(404, 'Brand not found.');
            }
        }

        $this->setBreadcrumb($category, $brand);

        return view('front-ecommerce::front.catalog', [
            'category' => $category,
            'brand' => $brand,
            'params' => $params,
            'breadcrumb' => static::$breadcrumb,
        ]);
    }

    /**
     * Set breadcrumb based on current listing context.
     */
    protected function setBreadcrumb($category = null, $brand = null): void
    {
        static::$breadcrumb = [
            'Home' => '/',
        ];

        if ($brand) {
            static::$breadcrumb['Brands'] = '/brands';
            static::$breadcrumb[$brand->name] = '';
        } elseif ($category) {
            static::$breadcrumb['Categories'] = '/categories';
            // Add parent category if exists
            if ($category->parent_id && $category->parent_id > 0) {
                $parent = $this->getCategoryModel()::find($category->parent_id);
                if ($parent) {
                    $parentName = $parent->currentLanguage->name ?? $parent->name ?? 'Parent';
                    static::$breadcrumb[$parentName] = '/category/' . ($parent->currentLanguage->link_rewrite ?? $parent->id);
                }
            }
            $categoryName = $category->name ?? 'Category';
            static::$breadcrumb[$categoryName] = '';
        } else {
            static::$breadcrumb['All Products'] = '';
        }
    }

    /**
     * Find category from URL params.
     */
    protected function findCategory(array $params): mixed
    {
        return $this->searchService->findCategory($params);
    }

    /**
     * Find brand from URL params.
     */
    protected function findBrand(array $params): mixed
    {
        return $this->searchService->findBrand($params);
    }

    /**
     * Get category model class from config or default.
     */
    protected function getCategoryModel(): ?string
    {
        return Config::get(
            'ud-front-ecommerce.category_model',
            \Unusualdope\LaravelEcommerce\Models\Product\ProductCategory::class
        );
    }

    /**
     * Get brand model class from config or default.
     */
    protected function getBrandModel(): ?string
    {
        return Config::get(
            'ud-front-ecommerce.brand_model',
            \Unusualdope\LaravelEcommerce\Models\Product\Brand::class
        );
    }
}
