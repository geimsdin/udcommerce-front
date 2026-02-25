<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;
use Unusualdope\FrontLaravelEcommerce\Support\TypesenseSearchService;

class PopularSearches extends Component
{
    public string $title = 'Abbigliamento, Accessori e Sneakers';

    public int $limit = 36;

    public int $columns = 6;

    public function mount(string $title = 'Abbigliamento, Accessori e Sneakers', int $limit = 36, int $columns = 6): void
    {
        $this->title = $title;
        $this->limit = $limit;
        $this->columns = $columns;
    }
    public function getLinkColumns(): \Illuminate\Support\Collection
    {
        $links = $this->getCategoryLinks();

        // Fallback to dummy data when no categories exist in DB
        if ($links->isEmpty()) {
            $links = $this->getDummyLinks();
        }

        if ($links->isEmpty()) {
            return collect();
        }

        $perColumn = (int) ceil($links->count() / $this->columns);

        return $links->chunk($perColumn);
    }

    protected function getCategoryLinks(): \Illuminate\Support\Collection
    {
        $searchService = app(TypesenseSearchService::class);
        $categories = $searchService->getPopularCategories($this->limit);

        return $categories->map(fn(ProductCategory $category) => (object) [
            'label' => $category->name,
            'url' => '/catalog?category=' . urlencode($category->currentLanguage?->name ?? $category->title),
        ]);
    }

    protected function getDummyLinks(): \Illuminate\Support\Collection
    {
        return collect([
            ['label' => 'T-Shirt The North Face', 'url' => '/catalog?category=t-shirt-the-north-face'],
            ['label' => 'Felpe', 'url' => '/catalog?category=felpe'],
            ['label' => 'Cappelli', 'url' => '/catalog?category=cappelli'],
            ['label' => 'Stivali Timberland', 'url' => '/catalog?category=stivali-timberland'],
            ['label' => 'T-Shirt con logo', 'url' => '/catalog?category=t-shirt-con-logo'],
            ['label' => 'Scarpe Uomo', 'url' => '/catalog?category=scarpe-uomo'],
            ['label' => 'Borse', 'url' => '/catalog?category=borse'],
            ['label' => 'Felpe con cappuccio', 'url' => '/catalog?category=felpe-con-cappuccio'],
            ['label' => 'Zainetti', 'url' => '/catalog?category=zainetti'],
            ['label' => 'Scarpe da Basket', 'url' => '/catalog?category=scarpe-da-basket'],
            ['label' => 'Tutte le t-shirts', 'url' => '/catalog?category=t-shirts'],
            ['label' => 'Scarpe Donna', 'url' => '/catalog?category=scarpe-donna'],
            ['label' => 'Scarpe Adidas', 'url' => '/catalog?category=scarpe-adidas'],
            ['label' => 'Giubbini', 'url' => '/catalog?category=giubbini'],
            ['label' => 'Berretti', 'url' => '/catalog?category=berretti'],
            ['label' => 'Sneakers', 'url' => '/catalog?category=sneakers'],
            ['label' => 'T-Shirt Nike', 'url' => '/catalog?category=t-shirt-nike'],
            ['label' => 'Outlet', 'url' => '/catalog?category=outlet'],
            ['label' => 'Scarpe Nike', 'url' => '/catalog?category=scarpe-nike'],
            ['label' => 'Felpe Girocollo', 'url' => '/catalog?category=felpe-girocollo'],
            ['label' => 'Sandali', 'url' => '/catalog?category=sandali'],
            ['label' => 'Giacche The North Face', 'url' => '/catalog?category=giacche-the-north-face'],
            ['label' => 'T-Shirt Adidas', 'url' => '/catalog?category=t-shirt-adidas'],
            ['label' => 'Accessori', 'url' => '/catalog?category=accessori'],
            ['label' => 'Converse All Star', 'url' => '/catalog?category=converse-all-star'],
            ['label' => 'Pantaloni Tuta', 'url' => '/catalog?category=pantaloni-tuta'],
            ['label' => 'Pochette', 'url' => '/catalog?category=pochette'],
            ['label' => 'T-Shirt Puma', 'url' => '/catalog?category=t-shirt-puma'],
            ['label' => 'Pantaloncini', 'url' => '/catalog?category=pantaloncini'],
            ['label' => 'Zaini Sprayground', 'url' => '/catalog?category=zaini-sprayground'],
            ['label' => 'Pantaloni', 'url' => '/catalog?category=pantaloni'],
            ['label' => 'Scarpe Lifestyle', 'url' => '/catalog?category=scarpe-lifestyle'],
            ['label' => 'Birkenstock', 'url' => '/catalog?category=birkenstock'],
            ['label' => 'Abbigliamento Lacoste', 'url' => '/catalog?category=abbigliamento-lacoste'],
            ['label' => 'Nuovi Arrivi', 'url' => '/catalog?category=nuovi-arrivi'],
            ['label' => 'Scarpe Lifestyle', 'url' => '/catalog?category=scarpe-lifestyle'],
        ])->take($this->limit)->map(fn(array $item) => (object) $item);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('front-ecommerce::livewire.front.home.popular-searches', [
            'linkColumns' => $this->getLinkColumns(),
        ]);
    }
}
