<div class="relative max-w-[150px]">
    <select
        wire:model.live="selectedCurrencyId"
        class="block w-full appearance-none rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm shadow-sm transition-all focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
    >
        @foreach ($currencies as $currency)
            <option value="{{ $currency->id }}">
                {{ $currency->iso_code }} - {{ $currency->symbol }}
            </option>
        @endforeach
    </select>

    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</div>