<div>
    {{-- Sort --}}
    <div class="flex justify-end gap-3 mb-4">
        <span class="text-sm text-gray-600">Ordina per:</span>
        <select name="order_by" wire:model.live="orderBy"
            class="border-0 border-b border-gray-300 bg-transparent text-sm focus:ring-0 focus:border-black py-1 pr-8 cursor-pointer">
            <option value="">Seleziona</option>
            <option value="price_asc">Prezzo: Basso-Alto</option>
            <option value="price_desc">Prezzo: Alto-Basso</option>
            <option value="name_asc">Nome: A-Z</option>
            <option value="name_desc">Nome: Z-A</option>
            <option value="newest">Più recenti</option>
        </select>
    </div>

    {{-- Filter Panel --}}
    <div class="bg-gray-50 border border-gray-200 rounded mb-8">
        {{-- Filter Header --}}
        <button type="button" @click="filtersOpen = !filtersOpen"
            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
            <span>Filtra e ordina</span>
            <svg class="w-4 h-4 transition-transform" :class="filtersOpen ? 'rotate-180' : ''" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Filter Options --}}
        <div x-show="filtersOpen" x-collapse class="border-t border-gray-200">
            <div class="grid grid-cols-4 gap-4 p-4">
                {{-- Brand Filter --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Brand</label>
                    <select name="brand" wire:model.live="brandId"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-black focus:border-black">
                        <option value="">Tutti i brand</option>
                        @foreach ($allBrands as $brandOption)
                            <option value="{{ $brandOption->id }}">{{ $brandOption->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Gender Filter (placeholder) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Genere</label>
                    <select name="gender"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-black focus:border-black"
                        disabled>
                        <option value="">Tutti i generi</option>
                        <option value="uomo">Uomo</option>
                        <option value="donna">Donna</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>

                {{-- Price Range Filter (placeholder) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Prezzo</label>
                    <div class="space-y-2 opacity-60 pointer-events-none">
                        <input type="range" min="0" max="500" x-model="priceRange"
                            class="w-full h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer accent-black">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>15,00 €</span>
                            <span x-text="priceRange + ',00 €'">100,00 €</span>
                        </div>
                    </div>
                </div>

                {{-- Category Filter --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Categoria</label>
                    <select name="category" wire:model.live="categoryId"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-black focus:border-black">
                        <option value="">Tutte le categorie</option>
                        @foreach ($allCategories as $cat)
                            <option value="{{ $cat->id }}">
                                {{ $cat->languages->first()?->name ?? $cat->name ?? 'Categoria #' . $cat->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Product Grid --}}
    @if($products->count() > 0)
        <div :class="viewMode === 'grid' ? 'grid grid-cols-3 gap-6' : 'flex flex-col gap-4'" itemscope
            itemtype="https://schema.org/ItemList">
            @foreach($products as $index => $product)
                <div class="group relative" :class="viewMode === 'list' ? 'flex gap-6 border-b border-gray-100 pb-4' : ''"
                    itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <meta itemprop="position" content="{{ $index + 1 }}" />

                    {{-- Wrapper for Schema Product --}}
                    <div itemprop="item" itemscope itemtype="https://schema.org/Product" class="contents">
                        <meta itemprop="url"
                            content="{{ url('/product/' . $product->id . '-' . ($product->currentLanguage->link_rewrite ?? $product->id)) }}" />
                        <meta itemprop="image"
                            content="{{ $product->images->first() ? $product->images->first()->image : '' }}" />

                        {{-- NEW Badge --}}
                        @if($product->created_at && $product->created_at->diffInDays(now()) < 30)
                            <div class="absolute top-2 left-2 z-10">
                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 uppercase">NEW</span>
                            </div>
                        @endif

                        {{-- Product Image --}}
                        <a href="/product/{{ $product->id }}-{{ $product->currentLanguage->link_rewrite ?? $product->id }}"
                            class="block bg-gray-50 overflow-hidden"
                            :class="viewMode === 'list' ? 'w-48 flex-shrink-0' : 'aspect-square'">
                            @if($product->images->count() > 0)
                                <img src="{{ $product->images->first()->image }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </a>

                        {{-- Product Info --}}
                        <div class="mt-4" :class="viewMode === 'list' ? 'flex-1 mt-0' : ''">
                            {{-- Size Options (if variable product) --}}
                            @if($product->type === 'variable' && $product->variations->count() > 0)
                                <div class="flex items-center gap-1 mb-2">
                                    @foreach($product->variations->take(3) as $variation)
                                        <span class="border border-gray-300 text-xs px-2 py-1 text-gray-600">
                                            {{ $variation->variants->first()?->name ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                    @if($product->variations->count() > 3)
                                        <span class="text-xs text-gray-500 ml-1">Taglia</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Product Name --}}
                            <h3 class="text-sm font-medium text-gray-900 uppercase tracking-wide mb-1 line-clamp-2"
                                itemprop="name">
                                <a href="/product/{{ $product->id }}-{{ $product->currentLanguage->link_rewrite ?? $product->id }}"
                                    class="hover:text-gray-600 transition-colors">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            {{-- Price --}}
                            <div class="text-sm text-gray-900" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                <meta itemprop="priceCurrency" content="EUR" />
                                <span itemprop="price"
                                    content="{{ $product->price }}">{{ number_format($product->price, 2, ',', '.') }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($pagination['last_page'] > 1)
            <div class="flex justify-center items-center gap-2 mt-12">
                {{-- Previous --}}
                @if($pagination['current_page'] > 1)
                    <button type="button" wire:click="previousPage"
                        class="px-3 py-2 text-sm text-gray-600 hover:text-black transition-colors">
                        &larr; Precedente
                    </button>
                @endif

                {{-- Page Numbers --}}
                @for($i = 1; $i <= $pagination['last_page']; $i++)
                    @if($i == $pagination['current_page'])
                        <span class="w-10 h-10 flex items-center justify-center text-sm font-medium bg-black text-white">
                            {{ $i }}
                        </span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $i }})"
                            class="w-10 h-10 flex items-center justify-center text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                            {{ $i }}
                        </button>
                    @endif
                @endfor

                {{-- Next --}}
                @if($pagination['current_page'] < $pagination['last_page'])
                    <button type="button" wire:click="nextPage"
                        class="px-3 py-2 text-sm text-gray-600 hover:text-black transition-colors">
                        Successivo &rarr;
                    </button>
                @endif
            </div>
        @endif
    @else
        {{-- No Products --}}
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p class="text-gray-500">Nessun prodotto trovato.</p>
        </div>
    @endif
</div>

