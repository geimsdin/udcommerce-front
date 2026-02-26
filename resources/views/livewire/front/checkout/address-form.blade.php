<div>
    <p class="text-sm text-gray-600 mb-4 font-bold uppercase">
        {{ __('front-ecommerce::checkout.form.shipping_address') }}
    </p>

    @if(!$isEditing && count($addresses) > 0)
        <div class="space-y-4 mb-8">
            @foreach($addresses as $address)
                <div
                    class="border p-6 relative {{ $selectedAddressId == $address->id ? 'border-black bg-white' : 'border-gray-200 bg-gray-50' }}">
                    <label class="flex items-start gap-4 cursor-pointer">
                        <input type="radio" wire:model.live="selectedAddressId" value="{{ $address->id }}"
                            class="mt-1 accent-black w-4 h-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-sm tracking-wide uppercase mb-4">
                                {{ $address->company ?: __('front-ecommerce::checkout.form.my_address_label') }}
                            </h3>
                            <div class="text-sm text-gray-600 leading-relaxed font-light">
                                <p>{{ $address->first_name }} {{ $address->last_name }}</p>
                                <p>{{ $address->address1 }}</p>
                                @if($address->address2)
                                <p>{{ $address->address2 }}</p>@endif
                                <p>{{ $address->postcode }} {{ $address->city }}</p>
                                <p>{{ $address->province }}</p>
                                <p>{{ $countries[$address->country] ?? $address->country }}</p>
                                <p>{{ $address->phone }}</p>
                            </div>
                        </div>
                    </label>

                    <div class="flex items-center gap-4 mt-6 sm:absolute sm:bottom-6 sm:right-6">
                        <button type="button" wire:click="editAddress({{ $address->id }})"
                            class="cursor-pointer border border-black px-6 py-2 text-xs uppercase tracking-wide hover:bg-gray-50 transition border-solid">
                            {{ __('front-ecommerce::checkout.form.actions.edit') }}
                        </button>
                        <button type="button" wire:click="deleteAddress({{ $address->id }})"
                            wire:confirm="{{ __('front-ecommerce::checkout.confirm.delete_address') }}"
                            class="cursor-pointer bg-[#2A2A2A] text-white px-6 py-2 text-xs uppercase tracking-wide hover:bg-black transition border border-transparent">
                            {{ __('front-ecommerce::checkout.form.actions.delete') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            <button type="button" wire:click="createNewAddress"
                class="flex items-center gap-2 text-sm text-gray-800 hover:text-black transition cursor-pointer">
                <span class="bg-[#2A2A2A] text-white w-4 h-4 flex items-center justify-center text-xs">+</span>
                <span>{{ __('front-ecommerce::checkout.form.actions.add') }}</span>
            </button>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-100">
            <label class="flex items-center gap-3 cursor-pointer mb-6">
                <input type="checkbox" wire:model.live="sameBillingAddress" class="accent-black w-4 h-4 rounded-none">
                <span class="text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.same_billing_label') }}</span>
            </label>

            @if(!$sameBillingAddress)
                <p class="text-sm text-gray-600 mb-4 font-bold uppercase mt-8 pt-8 border-t border-gray-100">
                    {{ __('front-ecommerce::checkout.form.billing_address') }}
                </p>
                <div class="space-y-4 mb-4">
                    @foreach($addresses as $address)
                        <div
                            class="border p-6 relative {{ $selectedBillingAddressId == $address->id ? 'border-black bg-white' : 'border-gray-200 bg-gray-50' }}">
                            <label class="flex items-start gap-4 cursor-pointer">
                                <input type="radio" wire:model.live="selectedBillingAddressId" value="{{ $address->id }}"
                                    class="mt-1 accent-black w-4 h-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-sm tracking-wide uppercase mb-4">
                                        {{ $address->company ?: __('front-ecommerce::checkout.form.my_address_label') }}
                                    </h3>
                                    <div class="text-sm text-gray-600 leading-relaxed font-light">
                                        <p>{{ $address->first_name }} {{ $address->last_name }}</p>
                                        <p>{{ $address->address1 }}</p>
                                        @if($address->address2)
                                        <p>{{ $address->address2 }}</p>@endif
                                        <p>{{ $address->postcode }} {{ $address->city }}</p>
                                        <p>{{ $address->province }}</p>
                                        <p>{{ $countries[$address->country] ?? $address->country }}</p>
                                        <p>{{ $address->phone }}</p>
                                    </div>
                                </div>
                            </label>

                            <div class="flex items-center gap-4 mt-6 sm:absolute sm:bottom-6 sm:right-6">
                                <button type="button" wire:click="editAddress({{ $address->id }})"
                                    class="cursor-pointer border border-black px-6 py-2 text-xs uppercase tracking-wide hover:bg-gray-50 transition border-solid">
                                    {{ __('front-ecommerce::checkout.form.actions.edit') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($errors->has('selectedBillingAddressId'))
                    <div class="text-right mt-2"><span
                            class="text-xs text-red-500">{{ $errors->first('selectedBillingAddressId') }}</span>
                    </div>
                @endif
            @endif
        </div>

        <div class="flex items-center justify-end pt-8">
            <button type="button" wire:click="submitAddress"
                class="bg-[#2A2A2A] text-white px-10 py-3 text-sm uppercase tracking-wide hover:bg-black transition">
                <span wire:loading.remove
                    wire:target="submitAddress">{{ __('front-ecommerce::checkout.form.continue') }}</span>
                <span wire:loading wire:target="submitAddress">{{ __('front-ecommerce::checkout.form.loading') }}</span>
            </button>
        </div>
        @if($errors->has('selectedAddressId'))
            <div class="text-right mt-2"><span class="text-xs text-red-500">{{ $errors->first('selectedAddressId') }}</span>
            </div>
        @endif
    @else
        <!-- Form for Add/Edit -->
        <form class="space-y-6" wire:submit.prevent="submitAddress">

            <!-- Nome destinazione (destination_name) -->
            <div class="grid grid-cols-12 items-start gap-4">
                <div class="col-span-3">
                    <label class="text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.destination_name') }}</label>
                    <p class="text-xs text-gray-400">{{ __('front-ecommerce::checkout.form.destination_helper') }}</p>
                </div>
                <div class="col-span-9">
                    <input type="text" wire:model="destination_name"
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('destination_name') border-red-500 @enderror"
                        placeholder="{{ __('front-ecommerce::checkout.form.destination_placeholder') }}">
                    @error('destination_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Nome -->
            <div class="grid grid-cols-12 items-center gap-4">
                <label
                    class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.first_name') }}</label>
                <div class="col-span-9">
                    <input type="text" wire:model="first_name" required
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('first_name') border-red-500 @enderror"
                        placeholder="{{ __('front-ecommerce::checkout.form.placeholders.first_name') }}">
                    @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Cognome -->
            <div class="grid grid-cols-12 items-center gap-4">
                <label class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.last_name') }}</label>
                <div class="col-span-9">
                    <input type="text" wire:model="last_name" required
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('last_name') border-red-500 @enderror"
                        placeholder="{{ __('front-ecommerce::checkout.form.placeholders.last_name') }}">
                    @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Azienda -->
            <div class="grid grid-cols-12 items-start gap-4">
                <div class="col-span-3">
                    <label class="text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.company') }}</label>
                    <p class="text-xs text-gray-400">{{ __('front-ecommerce::checkout.form.optional') }}</p>
                </div>
                <div class="col-span-9">
                    <input type="text" wire:model="company"
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('company') border-red-500 @enderror"
                        placeholder="">
                    @error('company') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Numero IVA -->
            <div class="grid grid-cols-12 items-start gap-4">
                <div class="col-span-3">
                    <label class="text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.vat_number') }}</label>
                    <p class="text-xs text-gray-400">{{ __('front-ecommerce::checkout.form.optional') }}</p>
                </div>
                <div class="col-span-9">
                    <input type="text" wire:model="vat_number"
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('vat_number') border-red-500 @enderror"
                        placeholder="">
                    @error('vat_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Indirizzo -->
            <div class="grid grid-cols-12 items-center gap-4">
                <label class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.address_1') }}</label>
                <div class="col-span-9">
                    <input type="text" wire:model="address1" required
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('address1') border-red-500 @enderror"
                        placeholder="{{ __('front-ecommerce::checkout.form.placeholders.address_1') }}">
                    @error('address1') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Complemento Indirizzo -->
            <div class="grid grid-cols-12 items-start gap-4">
                <div class="col-span-3">
                    <label class="text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.complement_label') }}</label>
                    <p class="text-xs text-gray-400">{{ __('front-ecommerce::checkout.form.optional') }}</p>
                </div>
                <div class="col-span-9">
                    <input type="text" wire:model="address2"
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('address2') border-red-500 @enderror"
                        placeholder="">
                    @error('address2') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Codice postale -->
            <div class="grid grid-cols-12 items-center gap-4">
                <label
                    class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.postal_code') }}</label>
                <div class="col-span-9">
                    <input type="text" wire:model="postcode" required
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('postcode') border-red-500 @enderror"
                        placeholder="{{ __('front-ecommerce::checkout.form.placeholders.postal_code') }}">
                    @error('postcode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Città -->
            <div class="grid grid-cols-12 items-center gap-4">
                <label class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.city') }}</label>
                <div class="col-span-9">
                    <input type="text" wire:model="city" required
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('city') border-red-500 @enderror"
                        placeholder="{{ __('front-ecommerce::checkout.form.placeholders.city') }}">
                    @error('city') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Provincia -->
            <div class="grid grid-cols-12 items-center gap-4">
                <label class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.province') }}</label>
                <div class="col-span-9">
                    <select wire:model="province"
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent appearance-none cursor-pointer @error('province') border-red-500 @enderror">
                        <option value="">{{ __('front-ecommerce::checkout.form.select') }}</option>
                        <option value="AG">Agrigento</option>
                        <option value="AL">Alessandria</option>
                        <option value="AN">Ancona</option>
                        <option value="AO">Aosta</option>
                        <option value="AR">Arezzo</option>
                        <option value="AP">Ascoli Piceno</option>
                        <option value="AT">Asti</option>
                        <option value="AV">Avellino</option>
                        <option value="BA">Bari</option>
                        <option value="BT">Barletta-Andria-Trani</option>
                        <option value="BL">Belluno</option>
                        <option value="BN">Benevento</option>
                        <option value="BG">Bergamo</option>
                        <option value="BI">Biella</option>
                        <option value="BO">Bologna</option>
                        <option value="BZ">Bolzano</option>
                        <option value="BS">Brescia</option>
                        <option value="BR">Brindisi</option>
                        <option value="CA">Cagliari</option>
                        <option value="CL">Caltanissetta</option>
                        <option value="CB">Campobasso</option>
                        <option value="CE">Caserta</option>
                        <option value="CT">Catania</option>
                        <option value="CZ">Catanzaro</option>
                        <option value="CH">Chieti</option>
                        <option value="CO">Como</option>
                        <option value="CS">Cosenza</option>
                        <option value="CR">Cremona</option>
                        <option value="KR">Crotone</option>
                        <option value="CN">Cuneo</option>
                        <option value="EN">Enna</option>
                        <option value="FM">Fermo</option>
                        <option value="FE">Ferrara</option>
                        <option value="FI">Firenze</option>
                        <option value="FG">Foggia</option>
                        <option value="FC">Forlì-Cesena</option>
                        <option value="FR">Frosinone</option>
                        <option value="GE">Genova</option>
                        <option value="GO">Gorizia</option>
                        <option value="GR">Grosseto</option>
                        <option value="IM">Imperia</option>
                        <option value="IS">Isernia</option>
                        <option value="SP">La Spezia</option>
                        <option value="AQ">L'Aquila</option>
                        <option value="LT">Latina</option>
                        <option value="LE">Lecce</option>
                        <option value="LC">Lecco</option>
                        <option value="LI">Livorno</option>
                        <option value="LO">Lodi</option>
                        <option value="LU">Lucca</option>
                        <option value="MC">Macerata</option>
                        <option value="MN">Mantova</option>
                        <option value="MS">Massa-Carrara</option>
                        <option value="MT">Matera</option>
                        <option value="ME">Messina</option>
                        <option value="MI">Milano</option>
                        <option value="MO">Modena</option>
                        <option value="MB">Monza e Brianza</option>
                        <option value="NA">Napoli</option>
                        <option value="NO">Novara</option>
                        <option value="NU">Nuoro</option>
                        <option value="OR">Oristano</option>
                        <option value="PD">Padova</option>
                        <option value="PA">Palermo</option>
                        <option value="PR">Parma</option>
                        <option value="PV">Pavia</option>
                        <option value="PG">Perugia</option>
                        <option value="PU">Pesaro e Urbino</option>
                        <option value="PE">Pescara</option>
                        <option value="PC">Piacenza</option>
                        <option value="PI">Pisa</option>
                        <option value="PT">Pistoia</option>
                        <option value="PN">Pordenone</option>
                        <option value="PZ">Potenza</option>
                        <option value="PO">Prato</option>
                        <option value="RG">Ragusa</option>
                        <option value="RA">Ravenna</option>
                        <option value="RC">Reggio Calabria</option>
                        <option value="RE">Reggio Emilia</option>
                        <option value="RI">Rieti</option>
                        <option value="RN">Rimini</option>
                        <option value="RM">Roma</option>
                        <option value="RO">Rovigo</option>
                        <option value="SA">Salerno</option>
                        <option value="SS">Sassari</option>
                        <option value="SV">Savona</option>
                        <option value="SI">Siena</option>
                        <option value="SR">Siracusa</option>
                        <option value="SO">Sondrio</option>
                        <option value="SU">Sud Sardegna</option>
                        <option value="TA">Taranto</option>
                        <option value="TE">Teramo</option>
                        <option value="TR">Terni</option>
                        <option value="TO">Torino</option>
                        <option value="TP">Trapani</option>
                        <option value="TN">Trento</option>
                        <option value="TV">Treviso</option>
                        <option value="TS">Trieste</option>
                        <option value="UD">Udine</option>
                        <option value="VA">Varese</option>
                        <option value="VE">Venezia</option>
                        <option value="VB">Verbano-Cusio-Ossola</option>
                        <option value="VC">Vercelli</option>
                        <option value="VR">Verona</option>
                        <option value="VV">Vibo Valentia</option>
                        <option value="VI">Vicenza</option>
                        <option value="VT">Viterbo</option>
                    </select>
                    @error('province') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-12 items-center gap-4">
                <label class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.country') }}</label>
                <div class="col-span-9">
                    <select wire:model.live="country" required
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent appearance-none cursor-pointer @error('country') border-red-500 @enderror">
                        @foreach($countries as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('country') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Telefono -->
            <div class="grid grid-cols-12 items-center gap-4">
                <label class="col-span-3 text-sm text-gray-700">{{ __('front-ecommerce::checkout.form.phone') }}</label>
                <div class="col-span-9">
                    <input type="tel" wire:model="phone" required
                        class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('phone') border-red-500 @enderror"
                        placeholder="{{ __('front-ecommerce::checkout.form.placeholders.phone') }}">
                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Dynamic Country Custom Fields -->
            @if(count($country_custom_fields_config) > 0)
                <div class="col-span-12">
                    <hr class="my-6 border-gray-200">
                </div>
                @foreach($country_custom_fields_config as $field)
                    <div class="grid grid-cols-12 items-start gap-4 mt-4">
                        <div class="col-span-3">
                            <label class="text-sm text-gray-700">{{ $field->label }}</label>
                            @if(!$field->is_required)
                                <p class="text-xs text-gray-400">{{ __('front-ecommerce::checkout.form.optional') }}</p>
                            @endif
                        </div>
                        <div class="col-span-9">
                            @if($field->type === 'textarea')
                                <textarea wire:model="custom_fields.{{ $field->name }}" {{ $field->is_required ? 'required' : '' }}
                                    class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('custom_fields.' . $field->name) border-red-500 @enderror"
                                    rows="3"></textarea>
                            @else
                                <input type="{{ $field->type === 'number' ? 'number' : 'text' }}"
                                    wire:model="custom_fields.{{ $field->name }}" {{ $field->is_required ? 'required' : '' }}
                                    class="w-full border-0 border-b border-gray-300 px-0 py-2 focus:ring-0 focus:border-black transition bg-transparent @error('custom_fields.' . $field->name) border-red-500 @enderror"
                                    placeholder="">
                            @endif
                            @error('custom_fields.' . $field->name)
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Buttons -->
            <div class="flex items-center justify-center gap-6 pt-8">
                <button type="submit"
                    class="bg-[#2A2A2A] text-white px-12 py-3 uppercase tracking-wide text-sm hover:bg-black transition disabled:opacity-50 cursor-pointer">
                    <span wire:loading.remove
                        wire:target="submitAddress">{{ __('front-ecommerce::checkout.form.continue') }}</span>
                    <span wire:loading wire:target="submitAddress">{{ __('front-ecommerce::checkout.form.loading') }}</span>
                </button>
                {{-- @if(count($addresses) > 0) --}}
                <button type="button" wire:click="cancelEdit"
                    class="text-sm text-gray-600 hover:text-black underline transition cursor-pointer">
                    {{ __('front-ecommerce::checkout.form.actions.cancel') }}
                </button>
                {{-- @endif --}}
            </div>

        </form>
    @endif
</div>