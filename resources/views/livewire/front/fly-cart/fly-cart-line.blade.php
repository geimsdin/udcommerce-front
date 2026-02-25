<div class="flex items-start gap-3 px-4 py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">

    {{-- Product Image --}}
    <div class="flex-shrink-0 w-18 h-18 rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
        <img
            src="{{ $product['product_image'] ? asset('storage/' . $product['product_image']) : asset('images/no-image.png') }}"
            alt="{{ $product['product_name'] }}"
            class="w-full h-full object-cover"
            style="width:72px;height:72px;"
        >
    </div>

    {{-- Info + Controls --}}
    <div class="flex-1 min-w-0 flex flex-col gap-1">

        {{-- Name + Remove button --}}
        <div class="flex items-start justify-between gap-2">
            <p class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">
                {{ $product['product_name'] }}
            </p>
            <button
                wire:click="removeFromCart"
                wire:loading.attr="disabled"
                class="flex-shrink-0 text-gray-300 hover:text-red-500 transition-colors mt-0.5"
                title="{{ __('front-ecommerce::cart.remove') }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Variants --}}
        @if (!empty($product['variants']))
            <div class="flex flex-wrap gap-1">
                @foreach ((array) $product['variants'] as $label => $value)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                        {{ $label }}: {{ $value }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Unit price --}}
        @php
            $rate = $currency->exchange_rate ?? 1;
        @endphp
        <p class="text-xs text-gray-400">
            {{ number_format($product['product_price'] * $rate, 2) }} {{ $currency->symbol ?? $currency->iso_code ?? '' }}
            / {{ __('front-ecommerce::cart.unit') }}
        </p>

        {{-- Quantity controls + Line total --}}
        <div class="flex items-center justify-between mt-1">

            {{-- –  qty  + --}}
            <div class="flex items-center gap-2">
                <button
                    wire:click="decreaseQuantity"
                    wire:loading.attr="disabled"
                    class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:border-gray-400 transition-colors disabled:opacity-40"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>

                <span class="w-6 text-center text-sm font-medium text-gray-900" wire:loading.class="opacity-40">
                    {{ $product['total_quantity'] }}
                </span>

                <button
                    wire:click="increaseQuantity"
                    wire:loading.attr="disabled"
                    class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:border-gray-400 transition-colors disabled:opacity-40"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>

            {{-- Line total --}}
            <span class="text-sm font-bold text-gray-900">
                {{ number_format($product['total_price'] * $rate, 2) }}
                {{ $currency->symbol ?? $currency->iso_code ?? '' }}
            </span>

        </div>
    </div>

</div>
