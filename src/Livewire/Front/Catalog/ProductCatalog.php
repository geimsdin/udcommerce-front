<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Catalog;

use Illuminate\Support\Facades\Config;
use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\FrontLaravelEcommerce\Support\TypesenseSearchService;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;

class ProductCatalog extends Component
{
    use WithPagination;

    /**
     * Keep these in the URL so the catalog is shareable/bookmarkable.
     */
    protected $queryString = [
        'brandId' => ['as' => 'brand_id', 'except' => ''],
        'categoryId' => ['as' => 'category_id', 'except' => ''],
        'orderBy' => ['as' => 'order_by', 'except' => ''],
    ];

    public $brandId = null;
    public $categoryId = null;
    public string $orderBy = '';

    public int $perPage = 16;

    public ?int $initialBrandId = null;
    public ?int $initialCategoryId = null;

    protected TypesenseSearchService $searchService;

    public function boot(TypesenseSearchService $searchService): void
    {
        // Avoid serializing the service into the Livewire payload.
        $this->searchService = $searchService;
    }

    public function mount(?int $initialBrandId = null, ?int $initialCategoryId = null): void
    {
        $this->initialBrandId = $initialBrandId;
        $this->initialCategoryId = $initialCategoryId;

        // If the URL (query string) didn't specify filters, seed them from the URL-mapped context.
        if (blank($this->brandId) && filled($initialBrandId)) {
            $this->brandId = $initialBrandId;
        }

        if (blank($this->categoryId) && filled($initialCategoryId)) {
            $this->categoryId = $initialCategoryId;
        }
    }

    public function updatingBrandId(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingOrderBy(): void
    {
        $this->resetPage();
    }

    protected function resolveOrder(string $orderBy): array
    {
        return match ($orderBy) {
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'name_asc' => ['language_name', 'asc'],
            'name_desc' => ['language_name', 'desc'],
            'newest' => ['created_at', 'desc'],
            default => ['created_at', 'desc'],
        };
    }

    protected function getBrandModel(): string
    {
        return Config::get('ud-front-ecommerce.brand_model', Brand::class);
    }

    protected function getCategoryModel(): string
    {
        return Config::get('ud-front-ecommerce.category_model', ProductCategory::class);
    }

    public function render()
    {
        $brandModel = $this->getBrandModel();
        $categoryModel = $this->getCategoryModel();

        $allBrands = $brandModel::query()
            // ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $allCategories = $categoryModel::query()
            ->where('status', 1)
            ->with(['languages'])
            ->orderBy('id')
            ->get();

        [$orderBy, $orderWay] = $this->resolveOrder($this->orderBy);

        $result = $this->searchService->searchProducts(
            categoryId: is_numeric($this->categoryId) ? (int) $this->categoryId : null,
            brandId: is_numeric($this->brandId) ? (int) $this->brandId : null,
            limit: $this->perPage,
            offset: ($this->getPage() - 1) * $this->perPage,
            orderBy: $orderBy,
            orderWay: $orderWay
        );

        $products = $result['products'];
        $totalProducts = $result['total'];

        $lastPage = $this->perPage > 0 ? (int) ceil($totalProducts / $this->perPage) : 1;

        return view('front-ecommerce::livewire.front.catalog.product-catalog', [
            'allBrands' => $allBrands,
            'allCategories' => $allCategories,
            'products' => $products,
            'pagination' => [
                'total' => $totalProducts,
                'per_page' => $this->perPage,
                'current_page' => $this->getPage(),
                'last_page' => $lastPage,
            ],
        ]);
    }
}

