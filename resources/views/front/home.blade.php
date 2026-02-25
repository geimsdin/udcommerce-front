@extends('front-ecommerce::front.layouts.app')

@section('title', __('Home'))

@section('content')
    <!-- HOMEPAGE MAIN CONTENT -->

    <div id="home" class="bg-white">

        <!-- HERO BANNER / WINTER SALE PROMO -->
        {{-- <section class="relative w-full">
            <div class="w-full h-[420px] flex items-center justify-center bg-gray-50"
                style="background-image: url('/path/to/your/background.jpg'); background-size: cover;">
                <div class="text-center w-full">
                    <img src="/path/to/logo-fight-usual.png" alt="Fight Usual RUSH Logo" class="mx-auto mb-6 h-16" />
                    <h1 class="text-5xl font-bold text-blue-900 leading-none">winter SALE</h1>
                    <div class="mt-10 flex justify-center gap-8 text-4xl font-bold">
                        <span class="text-green-500">20%<span class="block text-lg font-light">OFF</span></span>
                        <span class="text-yellow-400">30%<span
                                class="block text-lg font-light text-yellow-700">OFF</span></span>
                        <span class="text-red-600">50%<span class="block text-lg font-light text-red-700">OFF</span></span>
                    </div>
                    <!-- Decorative snowflakes if you wish -->
                    <div class="flex justify-center mt-6 gap-2 text-blue-300 text-2xl">&#10052;&#10052;&#10052;</div>
                </div>
            </div>
        </section> --}}
        @cmsbanner('homepage')

        <!-- SALE CAPTION / HIGHLIGHTS -->
        <section class="max-w-7xl mx-auto px-2 py-3">
            <div class="text-xs text-gray-600">Winter Sale<br><span class="text-black font-semibold">UP TO 50% OFF</span>
            </div>
        </section>

        <!-- SHOWCASE: TWO FEATURED PRODUCTS -->
        <section class="max-w-7xl mx-auto px-2 grid grid-cols-1 md:grid-cols-2 gap-4 pb-4">
            <div class="bg-gray-100 h-[420px] flex items-end justify-start w-full relative overflow-hidden rounded">
                <img src="/path/to/dickies-jacket.jpg" alt="Dickies Jacket" class="object-cover w-full h-full" />
                <div class="absolute bottom-4 left-4">
                    <div class="text-xs text-gray-700">Dickies</div>
                    <div class="text-[10px] text-gray-500 tracking-wide uppercase">JACKET</div>
                </div>
            </div>
            <div class="bg-gray-100 h-[420px] flex items-end justify-start w-full relative overflow-hidden rounded">
                <img src="/path/to/ugg-style.jpg" alt="Ugg Style" class="object-cover w-full h-full" />
                <div class="absolute bottom-4 left-4">
                    <div class="text-xs text-gray-700">Ugg</div>
                    <div class="text-[10px] text-gray-500 tracking-wide uppercase">UGG STYLE</div>
                </div>
            </div>
        </section>

        <!-- DISCOVER WEEKLY / PRODUCT CAROUSEL -->
        <livewire:front-ecommerce.home.discover-weekly limit="5" />


        <!-- LIFESTYLE/FASHION GRID PHOTOS -->
        <section class="max-w-7xl mx-auto px-2 py-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-100 rounded overflow-hidden relative h-[320px] flex items-end justify-start">
                <img src="/path/to/newera-ny.jpg" alt="New Era NY" class="object-cover w-full h-full" />
                <div class="absolute bottom-3 left-3">
                    <div class="text-xs text-gray-800">New Era</div>
                    <div class="text-[10px] text-gray-500">NY</div>
                </div>
            </div>
            <div class="bg-gray-100 rounded overflow-hidden relative h-[320px] flex items-end justify-start">
                <img src="/path/to/asics-sneaker.jpg" alt="Asics New In" class="object-cover w-full h-full" />
                <div class="absolute bottom-3 left-3">
                    <div class="text-xs text-gray-800">Asics</div>
                    <div class="text-[10px] text-gray-500">NEW IN</div>
                </div>
            </div>
            <div class="bg-gray-100 rounded overflow-hidden relative h-[320px] flex items-end justify-start">
                <img src="/path/to/tnf-jacket.jpg" alt="The North Face" class="object-cover w-full h-full" />
                <div class="absolute bottom-3 left-3">
                    <div class="text-xs text-gray-800">The North Face</div>
                    <div class="text-[10px] text-gray-500">APPAREL</div>
                </div>
            </div>
        </section>
        <section class="max-w-7xl mx-auto px-2 py-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-100 rounded overflow-hidden relative h-[420px] flex items-end justify-start">
                <img src="/path/to/sprayground-shark.jpg" alt="Sprayground Shark Bite" class="object-cover w-full h-full" />
                <div class="absolute bottom-3 left-3">
                    <div class="text-xs text-gray-800">Sprayground</div>
                    <div class="text-[10px] text-gray-500">SHARK BITE</div>
                </div>
            </div>
            <div class="bg-gray-100 rounded overflow-hidden relative h-[420px] flex items-end justify-start">
                <img src="/path/to/nike-af1-white.jpg" alt="Nike AF1" class="object-cover w-full h-full" />
                <div class="absolute bottom-3 left-3">
                    <div class="text-xs text-gray-800">Nike</div>
                    <div class="text-[10px] text-gray-500">AF1</div>
                </div>
            </div>
        </section>

        <!-- Brand Carousel Section -->
        <livewire:front-ecommerce.home.brand-carousel :limit="12" />

        <!-- Popular Searches Section -->
        <livewire:front-ecommerce.home.popular-searches title="Abbigliamento, Accessori e Sneakers" :columns="6" />

        <!-- Top Brands Section -->
        <livewire:front-ecommerce.home.top-brands :limit="20" :columns="5" />

    </div>
@endsection
