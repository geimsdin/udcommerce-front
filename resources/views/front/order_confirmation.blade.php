@extends('front-ecommerce::front.layouts.app')

@section('title', __('Cart'))

@section('content')
    <section id="order-confirmation" class="w-full px-4 py-10" x-data="{ activeTab: 'register' }">

        <!-- STEPS -->
        <div class="flex justify-between items-center mb-10 text-xs uppercase tracking-wide max-w-4xl mx-auto">
            <div class="flex items-center gap-2 text-black font-semibold">
                <span class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center text-xs">1</span>
                Informazioni personali
            </div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs">2</span>
                Indirizzi
            </div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs">3</span>
                Metodo di spedizione
            </div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs">4</span>
                Pagamento
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">

            <!-- LEFT: FORM -->
            <div class="lg:col-span-2 border rounded-md p-8">

                <!-- Tabs -->
                <div class="grid grid-cols-2 mb-8 text-sm uppercase text-center border">
                    <button type="button" @click="activeTab = 'register'"
                        :class="activeTab === 'register' ? 'font-semibold border-b-2 border-black text-black' : 'text-gray-400 border-b-2 border-transparent cursor-pointer'"
                        class="py-3 transition-colors">
                        Crea un account
                    </button>
                    <button type="button" @click="activeTab = 'login'"
                        :class="activeTab === 'login' ? 'font-semibold border-b-2 border-black text-black' : 'text-gray-400 border-b-2 border-transparent cursor-pointer'"
                        class="py-3 transition-colors">
                        Login
                    </button>
                </div>

                <!-- REGISTER TAB -->
                <div x-show="activeTab === 'register'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <livewire:front-ecommerce.auth.register-form />
                </div>

                <!-- LOGIN TAB -->
                <div x-show="activeTab === 'login'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="max-w-md mx-auto">
                        <p class="text-sm text-gray-600 mb-6">
                            Hai già un account? Accedi per completare l'ordine più velocemente.
                        </p>

                        <livewire:front-ecommerce.auth.login-form />
                    </div>
                </div>

            </div>

            <!-- RIGHT: SUMMARY -->
            <div class="lg:col-span-1">
                <livewire:front-ecommerce.checkout.checkout-summary />
            </div>
        </div>
        </div>
    </section>
@endsection