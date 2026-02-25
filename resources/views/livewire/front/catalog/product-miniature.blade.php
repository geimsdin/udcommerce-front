@push('structured-data')
    @if ($productSchema)
        <script type="application/ld+json">
            {!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endif
@endpush

<main class="max-w-6xl mx-auto px-4 py-8 lg:py-12" itemscope itemtype="https://schema.org/Product">
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Left: Gallery -->
        <section>
            <!-- Main image -->
            <div class="aspect-square bg-white rounded-xl shadow-sm flex items-center justify-center overflow-hidden">
                <img src="{{ $this->getImageUrl($mainImage) }}" alt="{{ $product->name }}" class="h-full w-full object-cover" itemprop="image" />
            </div>

            <!-- Thumbnails -->
            @if ($allImages->count() > 0)
                <div class="mt-4 grid grid-cols-4 gap-3">
                    @foreach ($allImages as $thumbnail)
                        <button 
                            wire:click="selectImage({{ $thumbnail->id }})"
                            wire:key="image-{{ $thumbnail->id }}"
                            type="button"
                            class="cursor-pointer aspect-square bg-white rounded-lg shadow-sm overflow-hidden {{ $this->isImageSelected($thumbnail) ? 'border-2 border-gray-900' : '' }} hover:border-gray-900 transition">
                            <img src="{{ $this->getImageUrl($thumbnail) }}"
                                alt="{{ $product->name }} - Image {{ $loop->index + 1 }}"
                                class="h-full w-full object-cover" />
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Right: Info & purchase -->
        <section class="space-y-6">
            <!-- Title & brand -->
            <div>
                @if ($product->brand)
                    <p class="text-xs font-semibold tracking-wide text-gray-500 uppercase" itemprop="brand" itemscope itemtype="https://schema.org/Brand">
                        <span itemprop="name">{{ $product->brand->name }}</span>
                    </p>
                @endif
                <h1 class="mt-1 text-2xl md:text-3xl font-semibold" itemprop="name">
                    {{ $product->name }}
                </h1>
            </div>

            <!-- Price & stock -->
            <div class="flex items-baseline gap-3" itemprop="offers" itemscope itemtype="https://schema.org/Offer" wire:key="price-{{ $quantity }}">
                @php
                    $variation = $selectedVariation ?? null;
                    $basePrice = $variation && $variation->price > 0 ? $variation->price : ($product->price ?? 0);
                    // Extract numeric price from formatted price for schema
                    $priceValue = preg_replace('/[^0-9,.]/', '', $formattedPrice);
                    $priceValue = str_replace(',', '.', $priceValue);
                @endphp
                <meta itemprop="price" content="{{ number_format((float)$priceValue, 2, '.', '') }}" />
                <meta itemprop="priceCurrency" content="{{ $productSchema['offers']['priceCurrency'] ?? 'EUR' }}" />
                <meta itemprop="availability" content="{{ $currentStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}" />
                <meta itemprop="url" content="{{ url($productUrl) }}" />
                <p class="text-2xl font-semibold">{{ $formattedPrice }}</p>
                @if ($currentStock > 0)
                    <p class="text-sm text-green-600 font-medium">{{ __('front-ecommerce::products.available') }}</p>
                @else
                    <p class="text-sm text-red-600 font-medium">{{ __('front-ecommerce::products.not_available') }}</p>
                @endif
            </div>

            <!-- Short description / badges -->
            <div class="space-y-2 text-sm text-gray-700" itemprop="description">
                {!! $this->getShortDescription() !!}
            </div>

            <!-- Variant selectors -->
            @if ($product->type === 'variable' && !empty($variationsByGroup))
                @foreach ($variationsByGroup as $group)
                    <div class="space-y-2" wire:key="group-{{ $group['id'] }}">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium">{{ $group['name'] }}</label>
                        </div>
                        <div @class([
                            'grid grid-cols-4 gap-2 text-sm' => !$group['is_color'],
                            'flex flex-wrap gap-2' => $group['is_color'],
                        ])>
                            @foreach ($group['variants'] as $variant)
                                @if ($variant['color'])
                                    <button 
                                        wire:click.prevent="selectVariant({{ $group['id'] }}, {{ $variant['id'] }})"
                                        wire:key="variant-{{ $group['id'] }}-{{ $variant['id'] }}"
                                        type="button"
                                        class="cursor-pointer h-10 w-10 border rounded-md py-2 text-center transition {{ $this->isVariantSelected($group['id'], $variant['id']) ? 'border-gray-900 bg-gray-100 font-semibold' : 'hover:border-gray-900' }}"
                                        style="background-color: {{ $variant['color'] }}; {{ $this->isVariantSelected($group['id'], $variant['id']) ? 'border-width: 2px;' : '' }}">
                                    </button>
                                @else
                                    <button 
                                        wire:click.prevent="selectVariant({{ $group['id'] }}, {{ $variant['id'] }})"
                                        wire:key="variant-{{ $group['id'] }}-{{ $variant['id'] }}"
                                        type="button"
                                        class="cursor-pointer border rounded-md py-2 text-center transition {{ $this->isVariantSelected($group['id'], $variant['id']) ? 'border-gray-900 bg-gray-100 font-semibold' : 'hover:border-gray-900' }}">
                                        {{ $variant['name'] }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
            @if (session('status'))
                <div class="text-sm text-red-600 font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Quantity selector -->
            <div class="space-y-2">
                <div>
                    <label class="text-sm font-medium">{{ __('front-ecommerce::products.quantity') }}</label>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        wire:click="decrementQuantity"
                        type="button"
                        class="cursor-pointer flex items-center justify-center w-10 h-10 border border-gray-300 rounded-md hover:bg-gray-100 transition {{ $this->quantity <= $this->getMinQuantity() ? 'opacity-50 cursor-not-allowed' : '' }}"
                        @if($this->quantity <= $this->getMinQuantity()) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                        </svg>
                    </button>
                    <input 
                        type="number"
                        wire:model.live="quantity"
                        min="{{ $this->getMinQuantity() }}"
                        max="{{ $this->getMaxQuantity() > 0 ? $this->getMaxQuantity() : '' }}"
                        class="w-20 text-center border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                    />
                    <button 
                        wire:click="incrementQuantity"
                        type="button"
                        class="cursor-pointer flex items-center justify-center w-10 h-10 border border-gray-300 rounded-md hover:bg-gray-100 transition {{ ($this->getMaxQuantity() > 0 && $this->quantity >= $this->getMaxQuantity()) ? 'opacity-50 cursor-not-allowed' : '' }}"
                        @if($this->getMaxQuantity() > 0 && $this->quantity >= $this->getMaxQuantity()) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
                @if ($this->isLowStock())
                    <p class="text-sm text-amber-600 font-medium">
                        {{ __('front-ecommerce::products.low_stock_warning', ['quantity' => $this->getCurrentStock()]) }}
                    </p>
                @endif
                {{-- @if ($this->getMaxQuantity() > 0)
                    <p class="text-xs text-gray-500">
                        {{ __('front-ecommerce::products.available_quantity', ['quantity' => $this->getMaxQuantity()]) }}
                    </p>
                @endif --}}
            </div>

            <!-- Action buttons -->
            <div class="space-y-3">
                <button
                    wire:click="addToCart"
                    class="cursor-pointer w-full inline-flex items-center justify-center rounded-md bg-black px-4 py-3 text-sm font-semibold text-white hover:bg-gray-900 transition">
                    {{ __('front-ecommerce::products.add_to_cart') }}
                </button>
                <button
                    class="cursor-pointer w-full inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-900 hover:bg-gray-100 transition">
                    {{ __('front-ecommerce::products.buy_now') }}
                </button>
            </div>
        </section>
    </div>

    <!-- Product description / details tabs -->
    <section class="mt-10 lg:mt-14">
        <div class="border-b border-gray-200 flex gap-6 text-sm">
            <button class="cursor-pointer py-2 border-b-2 border-gray-900 font-medium">
                {{ __('front-ecommerce::products.description') }}
            </button>
        </div>

        <div class="mt-4 grid gap-8 md:grid-cols-[2fr,1fr]">
            <!-- Long description -->
            <div class="space-y-3 text-sm leading-relaxed text-gray-700">
                @if ($product->description_long)
                    {!! $product->description_long !!}
                @elseif($product->description_short)
                    {!! $product->description_short !!}
                @else
                    <p class="text-gray-500">{{ __('front-ecommerce::products.no_description_available') }}</p>
                @endif
            </div>

            <!-- Technical info -->
            <aside class="bg-white rounded-xl border border-gray-200 p-4 space-y-3 text-sm">
                <h2 class="text-sm font-semibold">{{ __('front-ecommerce::products.technical_details') }}</h2>
                <dl class="space-y-2">
                    @if ($product->sku)
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">{{ __('front-ecommerce::products.product_code') }}</dt>
                            <dd class="font-medium text-right" itemprop="sku">{{ $product->sku }}</dd>
                        </div>
                    @endif
                    @if ($product->brand)
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">{{ __('front-ecommerce::products.brand') }}</dt>
                            <dd class="font-medium text-right">{{ $product->brand->name }}</dd>
                        </div>
                    @endif
                    @if ($product->categories && $product->categories->count() > 0)
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">{{ __('front-ecommerce::products.category') }}</dt>
                            <dd class="font-medium text-right">{{ $product->categories->first()->name ?? '-' }}</dd>
                        </div>
                    @endif
                </dl>

                {{-- Product Features --}}
                @if (!empty($productFeatures))
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-semibold mb-3">{{ __('front-ecommerce::products.features') }}</h3>
                        <div class="space-y-3">
                            @foreach ($productFeatures as $group)
                                <div>
                                    <h4 class="text-xs font-medium text-gray-700 mb-1">{{ $group['name'] }}</h4>
                                    <ul class="space-y-1">
                                        @foreach ($group['features'] as $feature)
                                            <li class="text-xs text-gray-600 flex items-center gap-2">
                                                <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $feature['name'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>
    <!-- END main product content -->
</main>
