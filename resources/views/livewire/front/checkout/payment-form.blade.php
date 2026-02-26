<div class="space-y-6">
    <div class="space-y-4">
        @foreach($gateways as $gateway)
            <div
                class="border p-6 relative {{ $selectedMethod == $gateway->getSlug() ? 'border-black bg-white' : 'border-gray-200 bg-gray-50' }}">
                <label class="flex items-center gap-4 cursor-pointer">
                    <input type="radio" wire:model.live="selectedMethod" value="{{ $gateway->getSlug() }}"
                        class="accent-black w-4 h-4">
                    <div class="flex-1 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-light text-gray-700">
                                @if($gateway->getSlug() == 'contrassegno')
                                    {{ __('front-ecommerce::checkout.payment.pay_via_cod') }}
                                @elseif($gateway->getSlug() == 'paypal')
                                    {{ __('front-ecommerce::checkout.payment.pay_with_paypal') }}
                                    <span class="inline-block ml-1">
                                        <svg class="w-4 h-4 text-gray-400 inline" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @elseif($gateway->getSlug() == 'stripe')
                                    {{ __('front-ecommerce::checkout.payment.pay_with_stripe') }}
                                @elseif($gateway->getSlug() == 'klarna')
                                    {{ __('front-ecommerce::checkout.payment.pay_with_klarna') }}
                                @else
                                    {{ $gateway->getName() }}
                                @endif
                            </span>

                            @if($gateway->getSlug() == 'paypal')
                                <div class="bg-white border rounded px-2 py-1 flex items-center">
                                    <span class="text-[#003087] font-bold italic text-xs">Pay</span><span
                                        class="text-[#009cde] font-bold italic text-xs">Pal</span>
                                </div>
                            @endif

                            @if($gateway->getSlug() == 'stripe')
                                <div class="bg-[#635BFF] text-white rounded px-2 py-0.5 flex items-center">
                                    <span class="font-bold text-[10px] tracking-tight uppercase">Stripe</span>
                                </div>
                            @endif

                            @if($gateway->getSlug() == 'klarna')
                                <span class="bg-[#FFB3C7] text-black text-[10px] font-bold px-1.5 py-0.5 rounded">Klarna</span>
                            @endif
                        </div>
                    </div>
                </label>

                @if($selectedMethod == 'paypal' && $gateway->getSlug() == 'paypal')
                    <div class="mt-6 ml-8">
                        <button type="button" wire:click.prevent="submit" wire:loading.attr="disabled"
                            class="bg-[#FFC439] hover:bg-[#F2BA36] text-[#003087] font-bold px-8 py-2 rounded-full flex items-center gap-2 transition shadow-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="submit">
                                <span>{{ __('front-ecommerce::checkout.payment.pay_with') }}</span>
                                <span class="italic font-extrabold flex inline">
                                    <span>Pay</span><span class="text-[#009cde]">Pal</span>
                                </span>
                            </span>
                            <span wire:loading wire:target="submit">{{ __('front-ecommerce::checkout.payment.loading') }}</span>
                        </button>
                    </div>
                @endif

                @if($selectedMethod == 'stripe' && $gateway->getSlug() == 'stripe')
                    <div class="mt-6 ml-8">
                        <button type="button" wire:click.prevent="submit" wire:loading.attr="disabled"
                            class="bg-[#635BFF] hover:bg-[#5851E0] text-white font-bold px-8 py-2 rounded flex items-center gap-2 transition shadow-sm disabled:opacity-50 text-sm">
                            <span wire:loading.remove wire:target="submit">
                                {{ __('front-ecommerce::checkout.payment.pay_with_card_stripe') }}
                            </span>
                            <span wire:loading wire:target="submit">{{ __('front-ecommerce::checkout.payment.loading') }}</span>
                        </button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @error('selectedMethod')
        <p class="text-sm text-red-500 font-medium px-4 py-3 bg-red-50 border border-red-100 rounded-lg">
            {{ $message }}
        </p>
    @enderror

    <!-- TERMS -->
    <div class="pt-6 border-t border-gray-100" x-data="{ terms: @entangle('termsAccepted') }">
        <label class="flex items-start gap-3 cursor-pointer group">
            <input type="checkbox" x-model="terms" class="mt-1 accent-black w-4 h-4 border-gray-300">
            <span class="text-sm text-gray-600 leading-relaxed">
                {{ __('front-ecommerce::checkout.payment.terms_accept') }} <a href="#"
                    class="underline hover:text-black transition">{{ __('front-ecommerce::checkout.payment.terms_of_service') }}</a>
                {{ __('front-ecommerce::checkout.payment.terms_accept_suffix') }}
            </span>
        </label>
        @error('termsAccepted')
            <p class="text-xs text-red-500 mt-2 ml-7">{{ $message }}</p>
        @enderror
    </div>

    @if($selectedMethod != 'paypal' && $selectedMethod != 'stripe')
        <div class="flex items-center justify-end pt-4">
            <button type="button" wire:click="submit"
                class="bg-[#2A2A2A] text-white px-12 py-3 text-sm uppercase tracking-wide hover:bg-black transition disabled:opacity-50">
                <span wire:loading.remove wire:target="submit">{{ __('front-ecommerce::checkout.payment.continue') }}</span>
                <span wire:loading wire:target="submit">{{ __('front-ecommerce::checkout.payment.loading') }}</span>
            </button>
        </div>
    @endif
</div>