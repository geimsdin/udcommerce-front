<div>
    @if(count($carriers) > 0)
        <div class="space-y-4 mb-8">
            @foreach($carriers as $carrier)
                <div
                    class="border p-6 relative {{ $selectedCarrierId == $carrier->id ? 'border-black bg-white' : 'border-gray-200 bg-gray-50' }}">
                    <label class="flex items-start gap-4 cursor-pointer">
                        <input type="radio" wire:model.live="selectedCarrierId" value="{{ $carrier->id }}"
                            class="mt-1 accent-black w-4 h-4">
                        <div class="flex-1 grid grid-cols-12 items-center">
                            <div class="col-span-12 sm:col-span-5">
                                <h3 class="font-bold text-sm tracking-wide uppercase">
                                    {{ $carrier->name }}
                                </h3>
                            </div>
                            <div class="col-span-6 sm:col-span-4 text-sm text-gray-500 font-light">
                                {{ $carrier->description }}
                            </div>
                            <div class="col-span-6 sm:col-span-3 text-right font-medium text-sm">
                                @if($carrier->price == 0)
                                    Gratis
                                @else
                                    {{ number_format($carrier->price, 2, ',', '.') }} €
                                @endif
                            </div>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        <div class="space-y-4 pt-4 border-t border-gray-100">
            <label class="block text-sm text-gray-700">
                Se desideri aggiungere un commento/nota sul tuo ordine, scrivi nel campo sottostante.
            </label>
            <textarea wire:model="notes" rows="6"
                class="w-full border border-gray-300 p-4 focus:ring-0 focus:border-black transition bg-transparent resize-none"
                placeholder=""></textarea>
        </div>

        <div class="flex items-center justify-end pt-8">
            <button type="button" wire:click="submit"
                class="bg-[#2A2A2A] text-white px-12 py-3 text-sm uppercase tracking-wide hover:bg-black transition">
                <span wire:loading.remove wire:target="submit">CONTINUA</span>
                <span wire:loading wire:target="submit">Caricamento...</span>
            </button>
        </div>

        @if($errors->has('selectedCarrierId'))
            <div class="text-right mt-2"><span class="text-xs text-red-500">{{ $errors->first('selectedCarrierId') }}</span>
            </div>
        @endif
    @else
        <div class="text-center py-10 text-gray-500">
            Nessun metodo di spedizione disponibile al momento.
        </div>
    @endif
</div>