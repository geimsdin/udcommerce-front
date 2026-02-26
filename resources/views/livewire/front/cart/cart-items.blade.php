<div class="lg:col-span-2 flex flex-col">

    <h1 class="text-2xl font-bold text-gray-900 tracking-tight mb-6">
        {{ __('front-ecommerce::cart.shopping_cart') }}
        @if ($total_quantity > 0)
            <span class="ml-2 text-base font-normal text-gray-400">({{ $total_quantity }} {{ __('front-ecommerce::cart.items') }})</span>
        @endif
    </h1>

    @if ($total_quantity > 0)

        {{-- Column headers --}}
        <div class="hidden md:grid grid-cols-12 gap-4 px-2 pb-2 border-b border-gray-200 text-xs font-semibold uppercase tracking-widest text-gray-400">
            <div class="col-span-6">{{ __('front-ecommerce::cart.product') }}</div>
            <div class="col-span-2 text-center">{{ __('front-ecommerce::cart.price') }}</div>
            <div class="col-span-2 text-center">{{ __('front-ecommerce::cart.quantity') }}</div>
            <div class="col-span-2 text-right">{{ __('front-ecommerce::cart.total') }}</div>
        </div>

        {{-- Product rows --}}
        <div class="flex flex-col divide-y divide-gray-100 mb-6">
            @foreach ($product_data as $product)
                <div
                    class="grid grid-cols-12 gap-4 items-center py-6"
                    wire:key="{{ $product['product_id'] }}_{{ $product['variation_id'] }}"
                    wire:loading.class.delay="opacity-50"
                >

                    {{-- Col: Image + Name --}}
                    <div class="col-span-12 md:col-span-6 flex items-center gap-4">
                        <a href="#" class="flex-shrink-0 block w-24 h-28 md:w-28 md:h-32 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                            <img
                                src="{{ $product['product_image'] ? asset('storage/' . $product['product_image']) : 'https://placehold.co/200x200?text=No+Image' }}"
                                alt="{{ $product['product_name'] }}"
                                class="w-full h-full object-cover"
                            >
                        </a>
                        <div class="flex flex-col gap-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 leading-snug">
                                {{ $product['product_name'] }}
                            </p>
                            @if (!empty($product['variants']))
                                <div class="flex flex-wrap gap-1 mt-0.5">
                                    @foreach ((array) $product['variants'] as $label => $value)
                                        <span class="text-xs text-gray-500 border border-gray-200 rounded px-1.5 py-0.5">
                                            {{ $label }}: <strong>{{ $value }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <button
                                wire:click="removeFromCart({{ $product['product_id'] }}, {{ $product['variation_id'] }})"
                                wire:loading.attr="disabled"
                                class="mt-2 self-start text-xs text-gray-400 hover:text-red-500 underline underline-offset-2 transition-colors"
                            >
                                {{ __('front-ecommerce::cart.remove') }}
                            </button>
                        </div>
                    </div>

                    {{-- Col: Unit price --}}
                    <div class="col-span-4 md:col-span-2 text-center">
                        <span class="text-sm font-medium text-gray-700">
                            {{ number_format($product['product_price'], 2) }}
                            {{ $currency->symbol ?? $currency->iso_code ?? '' }}
                        </span>
                    </div>

                    {{-- Col: Qty --}}
                    <div class="col-span-4 md:col-span-2 flex items-center justify-center">
                        <div class="inline-flex items-center border border-gray-300 rounded-lg overflow-hidden">
                            <button
                                wire:click="decreaseQuantity({{ $product['product_id'] }}, {{ $product['variation_id'] }})"
                                wire:loading.attr="disabled"
                                class="px-3 py-2 text-gray-500 hover:bg-gray-100 transition-colors text-lg leading-none disabled:opacity-40"
                            >−</button>
                            <span class="px-4 py-2 text-sm font-semibold text-gray-900 border-x border-gray-300 min-w-[2.5rem] text-center" wire:loading.class="opacity-40">
                                {{ $product['total_quantity'] }}
                            </span>
                            <button
                                wire:click="increaseQuantity({{ $product['product_id'] }}, {{ $product['variation_id'] }})"
                                wire:loading.attr="disabled"
                                class="px-3 py-2 text-gray-500 hover:bg-gray-100 transition-colors text-lg leading-none disabled:opacity-40"
                            >+</button>
                        </div>
                    </div>

                    {{-- Col: Line total --}}
                    <div class="col-span-4 md:col-span-2 text-right">
                        <span class="text-base font-bold text-gray-900">
                            {{ number_format($product['total_price'], 2) }}
                            {{ $currency->symbol ?? $currency->iso_code ?? '' }}
                        </span>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Continue shopping --}}
        <div class="pt-2">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition-colors group">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('front-ecommerce::cart.continue_shopping') }}
            </a>
        </div>

    @else
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-xl font-semibold text-gray-700 mb-2">{{ __('front-ecommerce::cart.cart_empty') }}</p>
            <a href="{{ url('/') }}" class="mt-4 text-sm text-gray-500 hover:text-gray-900 underline underline-offset-2 transition-colors">
                {{ __('front-ecommerce::cart.continue_shopping') }}
            </a>
        </div>
    @endif
</div>
