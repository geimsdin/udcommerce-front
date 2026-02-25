<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home;

use Livewire\Component;

class DiscoverWeekly extends Component
{
    public $limit = 5;

    public function mount($limit = 5)
    {
        $this->limit = $limit;
    }

    public function getProducts()
    {
        // Dummy data
        return collect([
            [
                'id' => 1,
                'name' => 'CAPPELLO NEW ERA 9FORTY...',
                'price' => '31,99 €',
                'image' => '/images/products/cap-leopard.jpg',
                'url' => '/product/1-cappello-new-era',
                'is_new' => true,
            ],
            [
                'id' => 2,
                'name' => 'SCARPE ASICS GEL-NYC...',
                'price' => '149,99 €',
                'image' => '/images/products/asics-gel.jpg',
                'url' => '/product/2-scarpe-asics-gel',
                'is_new' => false,
            ],
            [
                'id' => 3,
                'name' => 'SCARPE ON RUNNING...',
                'price' => '149,99 €',
                'image' => '/images/products/on-running.jpg',
                'url' => '/product/3-scarpe-on-running',
                'is_new' => true,
            ],
            [
                'id' => 4,
                'name' => 'SCARPE ADIDAS ADISTAR...',
                'price' => '109,99 €',
                'image' => '/images/products/adidas-adistar.jpg',
                'url' => '/product/4-scarpe-adidas-adistar',
                'is_new' => false,
            ],
            [
                'id' => 5,
                'name' => 'CAPPELLO NEW ERA 9FORTY...',
                'price' => '25,99 €',
                'image' => '/images/products/cap-newera-red.jpg',
                'url' => '/product/5-cappello-new-era-red',
                'is_new' => false,
            ],
        ])->take($this->limit);
    }

    public function render()
    {
        return view('front-ecommerce::livewire.front.home.discover-weekly', [
            'products' => $this->getProducts(),
        ]);
    }
}
