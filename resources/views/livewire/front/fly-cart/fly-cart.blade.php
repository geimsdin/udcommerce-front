<div>
    <!-- Shopping Cart Icon -->
    <a href="#" class="relative hover:text-gray-600 transition-colors" wire:click.prevent="openCart">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @if (count($product_data) > 0)
            <span
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center">
                {{ count($product_data) }}
            </span>
        @endif
    </a>

    <!-- Modal Backdrop -->
    <div x-data="{ show: @entangle('isOpen') }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-50"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-50"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black z-40 opacity-50 cursor-pointer"
        style="display: none;" wire:click="closeCart"></div>

    <!-- Cart Modal - Slide from Right -->
    <div x-data="{ show: @entangle('isOpen') }" x-show="show"
        x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-xl z-50 flex flex-col" style="display: none;">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <h2 class="text-lg font-semibold">{{ __('front-ecommerce::cart.shopping_cart') }}
                ({{ count($product_data) }})</h2>
            <button wire:click="closeCart" class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Cart Content -->
        <div class="flex-1 overflow-y-auto">
            @if (count($product_data) > 0)
                <div>
                    @foreach ($product_data as $product)
                        <livewire:front-ecommerce.fly-cart.fly-cart-line :product="$product" :cart_id="$cart['id']"
                            :currency="$currency" />
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-400 p-8">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-lg font-medium">{{ __('front-ecommerce::cart.cart_empty') }}</p>
                </div>
            @endif
        </div>

        <!-- Footer with Totals and Checkout -->
        @if ($total_quantity > 0)
            <div class="border-t p-4 space-y-4">
                <!-- Totals -->
                <div class="space-y-2">
                    @if(isset($totals->grand_total) && $currency)
                        @php
                            $rate = $currency->exchange_rate ?? 1;
                        @endphp
                        <div class="flex justify-between font-semibold text-lg">
                            <span>{{ __('front-ecommerce::cart.total') }}</span>
                            <span>{{ number_format($totals->grand_total * $rate, 2) }}
                                {{ $currency->symbol ?? $currency->iso_code ?? '' }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Checkout Button -->
                <a href="/order" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    {{ __('front-ecommerce::cart.proceed_to_checkout') }}
                </a>
            </div>
        @endif
    </div>
</div>