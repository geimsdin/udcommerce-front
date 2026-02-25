<section class="container mx-auto px-4 py-12">
    <h2 class="text-center text-3xl font-semibold mb-10">Discover Weekly</h2>

    <div class="grid grid-cols-5 gap-6">
        @forelse($products as $product)
            <a href="{{ $product['url'] }}" class="group block">
                <div class="relative">
                    {{-- NEW Badge --}}
                    @if($product['is_new'])
                        <span class="absolute top-0 left-0 bg-red-700 text-white text-xs font-medium px-3 py-1 z-10">
                            NEW
                        </span>
                    @endif

                    {{-- Product Image --}}
                    <div class="aspect-square bg-gray-50 flex items-center justify-center p-4 mb-4">
                        <img
                            src="{{ $product['image'] }}"
                            alt="{{ $product['name'] }}"
                            class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        >
                    </div>
                </div>

                {{-- Product Info --}}
                <div class="text-center">
                    <h3 class="text-sm font-medium text-gray-800 uppercase truncate mb-1">
                        {{ $product['name'] }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $product['price'] }}
                    </p>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                {{ __('No products available') }}
            </div>
        @endforelse
    </div>
</section>
