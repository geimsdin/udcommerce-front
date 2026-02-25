<div class="border rounded-md p-6 space-y-4 h-fit sticky top-4" x-data="{ open: false }">
    @if ($totals)
        <div class="space-y-4">
            @php
                $rate = $currency->exchange_rate ?? 1;
            @endphp

            <div class="flex justify-between text-sm items-center">
                <span class="text-gray-500">{{ $totals->total_quantity }}
                    {{ $totals->total_quantity > 1 ? 'articoli' : 'articolo' }}</span>

                <button @click="open = !open"
                    class="text-[10px] font-bold bg-[#b10a0a] text-white px-4 py-2 flex items-center gap-2 uppercase tracking-wider transition hover:bg-red-800">
                    <span x-text="open ? 'Nascondi dettagli' : 'Mostra dettagli'">Mostra dettagli</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="open ? '' : 'rotate-180'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
            </div>

            {{-- Accordion Content --}}
            <div x-show="open" x-collapse x-cloak class="space-y-6 pt-2">
                @foreach($product_data as $product)
                    <div class="flex gap-4 items-start">
                        <div class="w-20 h-20 bg-white border border-gray-100 flex-shrink-0 relative">
                            <img src="{{ $product['product_image'] ? asset('storage/' . $product['product_image']) : asset('images/no-image.png') }}"
                                alt="{{ $product['product_name'] }}" class="w-full h-full object-contain p-1">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2">
                                <h3 class="text-[14px] font-medium text-gray-700 uppercase leading-tight tracking-tight">
                                    {{ $product['product_name'] }}
                                    <span
                                        class="text-gray-400 normal-case font-normal ml-0.5 whitespace-nowrap">x{{ $product['total_quantity'] }}</span>
                                </h3>
                                <span class="text-[14px] font-bold text-gray-900 flex-shrink-0">
                                    {{ number_format($product['total_price'] * $rate, 2, ',', '.') }} {{ $currency->symbol ?? '€' }}
                                </span>
                            </div>
                            @if(!empty($product['variants']))
                                <div class="text-[13px] text-gray-500 mt-1">
                                    @foreach ((array) $product['variants'] as $label => $value)
                                        <p>{{ $label }}: {{ $value }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between text-sm pt-4">
                <span class="text-gray-600">Totale parziale</span>
                <span class="font-bold">
                    {{ number_format(($totals->total_amount_no_taxes + $totals->total_taxes) * $rate, 2, ',', '.') }}
                    {{ $currency->symbol ?? '€' }}
                </span>
            </div>

            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Spedizione</span>
                @if ($totals->shipping_cost > 0)
                    <span class="font-bold">
                        {{ number_format($totals->shipping_cost * $rate, 2, ',', '.') }}
                        {{ $currency->symbol ?? '€' }}
                    </span>
                @elseif(session()->has('checkout.shipping_method'))
                    <span class="font-bold text-gray-900 tracking-tight">Gratis</span>
                @else
                    <span class="font-semibold text-gray-400 italic">Da calcolare</span>
                @endif
            </div>

            @if ($totals->payment_fee > 0)
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Commissione {{ session('checkout.payment_method.name', 'pagamento') }}</span>
                    <span class="font-bold">
                        {{ number_format($totals->payment_fee * $rate, 2, ',', '.') }}
                        {{ $currency->symbol ?? '€' }}
                    </span>
                </div>
            @endif

            <hr class="border-gray-100">

            <div class="flex justify-between text-[14px] text-gray-700">
                <span>Totale (Tasse incluse)</span>
                <span class="font-bold text-gray-900">
                    {{ number_format($totals->grand_total * $rate, 2, ',', '.') }}
                    {{ $currency->symbol ?? '€' }}
                </span>
            </div>

            <div class="pt-2">
                <button class="text-[15px] font-medium text-gray-500 hover:text-gray-800 transition">
                    Hai un codice promozionale?
                </button>
            </div>
        </div>

        <!-- Info -->
        <div class="pt-6 space-y-4 text-sm text-gray-700 border-t mt-4">
            <div class="flex gap-3 items-start">
                <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span>Pagamento sicuro SSL</span>
            </div>
            <div class="flex gap-3 items-start text-xs text-gray-500">
                <span>I prezzi includono l'IVA applicabile secondo le normative vigenti.</span>
            </div>
        </div>
    @else
        <div class="text-center py-4 text-gray-400 italic">
            Nessun totale disponibile.
        </div>
    @endif
</div>