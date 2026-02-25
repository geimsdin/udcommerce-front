<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\FrontLaravelEcommerce\Support\TypesenseSearchService;

class TopBrands extends Component
{
    public int $limit = 20;

    public int $columns = 5;

    public function mount(int $limit = 20, int $columns = 5): void
    {
        $this->limit = $limit;
        $this->columns = $columns;
    }

    public function getBrandColumns(): \Illuminate\Support\Collection
    {
        $searchService = app(TypesenseSearchService::class);
        $brands = $searchService->getTopBrands($this->limit);

        // Fallback to dummy data when no brands exist in DB
        if ($brands->isEmpty()) {
            $brands = $this->getDummyBrands();
        }

        $perColumn = (int) ceil($brands->count() / $this->columns);

        return $brands->chunk($perColumn);
    }

    protected function getDummyBrands(): \Illuminate\Support\Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Nike'],
            ['id' => 2, 'name' => 'Adidas'],
            ['id' => 3, 'name' => 'Birkenstock'],
            ['id' => 4, 'name' => 'Converse'],
            ['id' => 5, 'name' => 'Jordan NBA'],
            ['id' => 6, 'name' => 'Fred Perry'],
            ['id' => 7, 'name' => 'New Era'],
            ['id' => 8, 'name' => 'New Balance'],
            ['id' => 9, 'name' => 'The North Face'],
            ['id' => 10, 'name' => 'Asics'],
            ['id' => 11, 'name' => 'Ugg'],
            ['id' => 12, 'name' => 'Lacoste'],
            ['id' => 13, 'name' => 'Puma'],
            ['id' => 14, 'name' => 'On Running'],
            ['id' => 15, 'name' => 'Timberland'],
            ['id' => 16, 'name' => 'Nike Basketball'],
            ['id' => 17, 'name' => 'Alpha Industries'],
            ['id' => 18, 'name' => 'Sprayground'],
            ['id' => 19, 'name' => 'Dickies'],
            ['id' => 20, 'name' => 'Nike NBA'],
        ])->take($this->limit)->map(fn(array $item) => (object) $item);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('front-ecommerce::livewire.front.home.top-brands', [
            'brandColumns' => $this->getBrandColumns(),
        ]);
    }
}
