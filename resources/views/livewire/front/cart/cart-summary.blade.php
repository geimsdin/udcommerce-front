<div class="bg-gray-50 rounded-2xl p-6 space-y-5 h-fit sticky top-6 border border-gray-100">

    <h2 class="text-base font-bold text-gray-900 uppercase tracking-widest">
        {{ __('front-ecommerce::cart.order_summary') }}
    </h2>

    @if ($totals)

        {{-- Line items --}}
        <div class="space-y-3 text-sm">

            @php
                $rate = $currency->exchange_rate ?? 1;
            @endphp

            <div class="flex justify-between text-gray-600">
                <span>{{ __('front-ecommerce::cart.subtotal') }}</span>
                <span class="font-medium text-gray-900">
                    {{ number_format($totals->total_amount_no_taxes * $rate, 2) }}
                    {{ $currency->symbol ?? ($currency->iso_code ?? '') }}
                </span>
            </div>

            <div class="flex justify-between text-gray-500">
                <span>{{ __('front-ecommerce::cart.taxes') }}</span>
                <span>
                    {{ number_format($totals->total_taxes * $rate, 2) }}
                    {{ $currency->symbol ?? ($currency->iso_code ?? '') }}
                </span>
            </div>

            <div class="flex justify-between text-gray-500">
                <span>{{ __('front-ecommerce::cart.shipping') }}</span>
                <span>
                    @if ($totals->shipping_cost > 0)
                        {{ number_format($totals->shipping_cost * $rate, 2) }}
                        {{ $currency->symbol ?? ($currency->iso_code ?? '') }}
                    @else
                        <span class="text-emerald-600 font-semibold">{{ __('front-ecommerce::cart.free') }}</span>
                    @endif
                </span>
            </div>

            {{-- Discount row (shown only when a coupon is applied) --}}
            @if ($appliedCoupon && $discountAmount > 0)
                <div class="flex justify-between text-emerald-600 font-medium">
                    <span>
                        {{ __('front-ecommerce::cart.discount') }}
                        <span class="text-xs font-normal text-emerald-500">({{ $appliedCoupon['code'] }})</span>
                    </span>
                    <span>
                        −{{ number_format($discountAmount * $rate, 2) }}
                        {{ $currency->symbol ?? ($currency->iso_code ?? '') }}
                    </span>
                </div>
            @endif

        </div>

        {{-- Coupon Section --}}
        <div class="border-t border-dashed border-gray-200 pt-4">

            @if ($appliedCoupon)
                {{-- Applied state --}}
                <div
                    class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2 text-emerald-700 text-sm font-semibold">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ $appliedCoupon['code'] }}</span>
                        <span class="text-xs font-normal text-emerald-600">
                            — {{ __('front-ecommerce::cart.coupon_applied') }}
                        </span>
                    </div>
                    <button wire:click="removeCoupon"
                        class="text-xs text-gray-400 hover:text-red-500 underline underline-offset-2 transition-colors ml-3 flex-shrink-0">
                        {{ __('front-ecommerce::cart.remove_coupon') }}
                    </button>
                </div>
            @else
                {{-- Input state --}}
                <div class="flex gap-2">
                    <input type="text" wire:model="couponCode" wire:keydown.enter="applyCoupon"
                        placeholder="{{ __('front-ecommerce::cart.coupon_code') }}"
                        class="flex-1 min-w-0 border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent transition" />
                    <button wire:click="applyCoupon" wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        class="px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 active:scale-[0.97] transition-all flex-shrink-0">
                        {{ __('front-ecommerce::cart.apply_coupon') }}
                    </button>
                </div>

                @if ($couponError)
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $couponError }}
                    </p>
                @endif
            @endif

        </div>

        {{-- Divider --}}
        <div class="border-t-2 border-dashed border-gray-200 pt-4">
            <div class="flex justify-between items-baseline">
                <span
                    class="text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ __('front-ecommerce::cart.total') }}</span>
                <span class="text-2xl font-extrabold text-gray-900">
                    {{ number_format(max(0, ($totals->grand_total - $discountAmount) * $rate), 2) }}
                    <span
                        class="text-sm font-semibold text-gray-500">{{ $currency->symbol ?? ($currency->iso_code ?? '') }}</span>
                </span>
            </div>
            @if ($totals->total_taxes > 0)
                <p class="text-xs text-gray-400 mt-1">({{ __('front-ecommerce::cart.tax_included') }} {{ $totals->tax }})</p>
            @endif
        </div>

        {{-- Checkout CTA --}}
        <a href="#"
            class="flex items-center justify-center gap-2 w-full bg-gray-900 text-white py-3.5 rounded-xl text-sm font-bold tracking-wide hover:bg-gray-700 active:scale-[0.98] transition-all">
            {{ __('front-ecommerce::cart.proceed_to_checkout') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- Trust row --}}
        <div class="grid grid-cols-3 gap-2 pt-2 text-center text-xs text-gray-400">
            <div class="flex flex-col items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" />
                </svg>
                <span>{{ __('front-ecommerce::cart.free_shipping_info') }}</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                </svg>
                <span>{{ __('front-ecommerce::cart.return_policy') }}</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>{{ __('front-ecommerce::cart.secure_payment') }}</span>
            </div>
        </div>
    @else
        <p class="text-sm text-gray-400 py-4 text-center">{{ __('front-ecommerce::cart.cart_empty') }}</p>
    @endif

</div>
