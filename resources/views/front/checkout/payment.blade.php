@extends('front-ecommerce::front.layouts.app')

@section('title', __('Pagamento') . ' - Checkout')

@section('content')
    <section id="checkout-payment" class="w-full px-4 py-10">

        <!-- STEPS -->
        <div class="flex justify-between items-center mb-10 text-xs uppercase tracking-wide max-w-4xl mx-auto">
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-xs">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                Informazioni personali
            </div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-xs">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                Indirizzi
            </div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-xs">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                Metodo di spedizione
            </div>
            <div class="flex items-center gap-2 text-black font-semibold">
                <span class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center text-xs">4</span>
                Pagamento
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">

            <!-- LEFT: PAYMENT FORM -->
            <div class="lg:col-span-2">
                <div class="border rounded-md p-8">
                    <livewire:front-ecommerce.checkout.payment-form />
                </div>
            </div>

            <!-- RIGHT: SUMMARY -->
            <div class="lg:col-span-1">
                <livewire:front-ecommerce.checkout.checkout-summary />
            </div>
        </div>
    </section>
@endsection