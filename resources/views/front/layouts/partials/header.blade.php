<!-- Header Component -->
<header class="w-full">
    <!-- Promotional Banner -->
    <div class="bg-red-600 text-white text-center py-2 text-sm font-medium tracking-wide">
        WINTER SALE - UP TO 50% OFF
    </div>

    <!-- Logo Section -->
    <div class="flex justify-center py-6 border-b border-gray-100">
        <a href="/" class="block">
            <div class="bg-black text-white font-bold text-2xl leading-tight px-2 py-1 tracking-widest">
                <div class="flex">
                    <span class="transform scale-x-[-1]">R</span>
                    <span>U</span>
                </div>
                <div class="flex">
                    <span>S</span>
                    <span>H</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Navigation Bar -->
    <nav class="border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Main Navigation -->
                <ul class="flex items-center space-x-8 text-sm font-medium tracking-wide">
                    <li>
                        <a href="#" class="hover:text-gray-600 transition-colors">NUOVI ARRIVI</a>
                    </li>
                    <li class="relative group">
                        <a href="#" class="hover:text-gray-600 transition-colors flex items-center gap-1">
                            SCARPE
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </li>
                    <li class="relative group">
                        <a href="#" class="hover:text-gray-600 transition-colors flex items-center gap-1">
                            T-SHIRTS
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </li>
                    <li class="relative group">
                        <a href="#" class="hover:text-gray-600 transition-colors flex items-center gap-1">
                            PANTALONI
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </li>
                    <li class="relative group">
                        <a href="#" class="hover:text-gray-600 transition-colors flex items-center gap-1">
                            FELPE
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </li>
                    <li class="relative group">
                        <a href="#" class="hover:text-gray-600 transition-colors flex items-center gap-1">
                            GIACCHE
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </li>
                    <li class="relative group">
                        <a href="#" class="hover:text-gray-600 transition-colors flex items-center gap-1">
                            ACCESSORI
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </li>
                    <li class="relative group">
                        <a href="#" class="hover:text-gray-600 transition-colors flex items-center gap-1">
                            BRANDS
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-gray-600 transition-colors">OUTLET</a>
                    </li>
                </ul>

                <!-- Utility: Currency + Icons -->
                <div class="flex items-center space-x-5">
                    <!-- Currency Switcher -->
                    <livewire:front-ecommerce.currency-switcher />

                    <!-- Search -->
                    <button class="hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    <!-- User Account -->
                    <a href="#" class="hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                    <livewire:front-ecommerce.fly-cart.fly-cart />
                </div>
            </div>
        </div>
    </nav>
</header>