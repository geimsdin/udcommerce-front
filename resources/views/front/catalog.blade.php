@extends('front-ecommerce::front.layouts.app')

@section('title')
    {{ $category->name ?? $brand->name ?? 'Product Catalog' }}
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="catalogFilters()">
        {{-- Page Title --}}
        <h1 class="text-2xl font-bold text-center uppercase tracking-wide mb-8">
            @if($category)
                {{ $category->name }}
            @elseif($brand)
                {{ $brand->name }}
            @else
                All Products
            @endif
        </h1>

        {{-- Toolbar: View Toggle & Sort --}}
        <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-4">
            {{-- View Toggle --}}
            <div class="flex items-center gap-2">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'text-black' : 'text-gray-400'"
                    class="p-1 hover:text-black transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'text-black' : 'text-gray-400'"
                    class="p-1 hover:text-black transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

        </div>

        <livewire:front-ecommerce.catalog.product-catalog
            :initial-brand-id="$brand?->id"
            :initial-category-id="$category?->id"
        />
    </div>

    <script>
        function catalogFilters() {
            return {
                viewMode: 'grid',
                filtersOpen: true,
                priceRange: 100,
            }
        }
    </script>
@endsection
