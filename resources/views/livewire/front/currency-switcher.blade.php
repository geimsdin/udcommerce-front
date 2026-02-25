<div>
    <select
        wire:model.live="selectedCurrencyId"
        class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-gray-500"
    >
        @foreach ($currencies as $currency)
            <option value="{{ $currency->id }}">
                {{ $currency->iso_code }}
            </option>
        @endforeach
    </select>
</div>

