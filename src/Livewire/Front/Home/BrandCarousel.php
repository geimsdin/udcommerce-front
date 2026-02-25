<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Home;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;

class BrandCarousel extends Component
{
    /** @var int Maximum number of brands to display */
    public int $limit = 12;

    public function mount(int $limit = 12): void
    {
        $this->limit = $limit;
    }

    /**
     * Get brands for the carousel (with logo/image if available).
     *
     * @return \Illuminate\Support\Collection<int, Brand|object>
     */
    public function getBrands(): \Illuminate\Support\Collection
    {
        $brands = Brand::query()
            ->orderBy('name')
            ->limit($this->limit)
            ->get();

        if ($brands->isEmpty()) {
            $brands = $this->getDummyBrands();
        }

        return $brands;
    }

    /**
     * Dummy brands for development/demo purposes.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    protected function getDummyBrands(): \Illuminate\Support\Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Nike', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg'],
            ['id' => 2, 'name' => 'Adidas', 'image' => 'https://hypesneakerid.com/wp-content/uploads/2024/07/Screenshot-2024-07-30-192423-600x600.png'],
            ['id' => 3, 'name' => 'Birkenstock', 'image' => 'https://logowik.com/content/uploads/images/birkenstock2523.logowik.com.webp'],
            ['id' => 4, 'name' => 'Converse', 'image' => 'https://shoesandcare.com/storage/gambar_post/fileName1695113058.png'],
            ['id' => 5, 'name' => 'Jordan NBA', 'image' => '/images/brands/jordan.png'],
            ['id' => 6, 'name' => 'Fred Perry', 'image' => '/images/brands/fredperry.png'],
            ['id' => 7, 'name' => 'New Era', 'image' => '/images/brands/newera.png'],
            ['id' => 8, 'name' => 'New Balance', 'image' => '/images/brands/newbalance.png'],
            ['id' => 9, 'name' => 'The North Face', 'image' => '/images/brands/tnf.png'],
            ['id' => 10, 'name' => 'Asics', 'image' => '/images/brands/asics.png'],
            ['id' => 11, 'name' => 'Ugg', 'image' => '/images/brands/ugg.png'],
            ['id' => 12, 'name' => 'Lacoste', 'image' => '/images/brands/lacoste.png'],
        ])->take($this->limit)->map(fn (array $item) => (object) $item);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('front-ecommerce::livewire.front.home.brand-carousel', [
            'brands' => $this->getBrands(),
        ]);
    }
}
