@extends('front-ecommerce::front.layouts.app')

@section('title', __('Ordine completato') . ' - Checkout')

@section('content')
    <section id="order-success" class="w-full px-4 py-20 text-center">
        <div class="max-w-2xl mx-auto space-y-8">

            <!-- Success Icon -->
            <div class="flex justify-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <div class="space-y-4">
                <h1 class="text-3xl font-bold uppercase tracking-wider">Grazie per il tuo ordine!</h1>
                <p class="text-gray-600">Il tuo ordine è stato ricevuto ed è in fase di elaborazione.</p>
            </div>

            <div class="border rounded-lg p-8 bg-gray-50 space-y-4 text-left">
                <h2 class="font-semibold text-lg border-b pb-4 mb-4">Riepilogo dell'ordine</h2>

                <div class="space-y-2 text-sm">
                    @if($order)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Riferimento ordine:</span>
                            <span class="font-medium">#{{ $order->reference }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metodo di pagamento:</span>
                        <span class="font-medium">{{ $order->payment_method ?? session('checkout.payment_method.name') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Stato dell'ordine:</span>
                        <span class="font-medium">{{ $order->status ?? 'In attesa' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metodo di spedizione:</span>
                        <span class="font-medium">{{ $order->shipping_method ?? session('checkout.shipping_method.name', 'Standard') }}</span>
                    </div>
                    
                    @if(isset($order->payment_fee) && $order->payment_fee > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Commissione pagamento:</span>
                            <span class="font-medium">{{ number_format($order->payment_fee, 2, ',', '.') }} €</span>
                        </div>
                    @elseif(session('checkout.payment_method.fee'))
                        <div class="flex justify-between">
                            <span class="text-gray-500">Commissione contrassegno:</span>
                            <span class="font-medium">{{ number_format(session('checkout.payment_method.fee'), 2, ',', '.') }} €</span>
                        </div>
                    @endif
                </div>

                @php
                    $bank_details = $order->payment_info ? (json_decode($order->payment_info, true)['bank_details'] ?? null) : session('checkout.payment_method.bank_details');
                    $finish_message = $order->payment_info ? (json_decode($order->payment_info, true)['finish_message'] ?? null) : session('checkout.payment_method.finish_message');
                @endphp

                @if($bank_details)
                    <div class="mt-6 p-4 bg-white border border-blue-100 rounded text-sm space-y-3">
                        <h3 class="font-semibold text-blue-800 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Istruzioni per il bonifico
                        </h3>
                        <div class="text-gray-700 whitespace-pre-line leading-relaxed">
                            {{ $bank_details }}</div>
                        @if($finish_message)
                            <p class="text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                {{ $finish_message }}</p>
                        @endif
                    </div>
                @endif

                <div class="pt-4 border-t flex justify-between font-bold text-xl uppercase">
                    <span>Totale</span>
                    <span>{{ $order ? number_format($order->grand_total, 2, ',', '.') . ' €' : session('checkout.total', '79,99 €') }}</span>
                </div>
            </div>

            <p class="text-sm text-gray-500 italic">
                Riceverai a breve un'e-mail di conferma con tutti i dettagli del tuo ordine.
            </p>

            <div class="pt-6">
                <a href="/"
                    class="inline-block bg-black text-white px-12 py-4 text-sm uppercase tracking-widest hover:bg-gray-800 transition shadow-lg">
                    Torna alla Home
                </a>
            </div>
        </div>
    </section>
@endsection