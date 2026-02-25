<form wire:submit.prevent="register" class="space-y-6">

    <!-- Title -->
    <div>
        <label class="block text-sm mb-2">Titolo sociale</label>
        <div class="flex gap-6 text-sm">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="title" value="sig" class="accent-black">
                Sig.
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="title" value="sigra" class="accent-black">
                Sig.ra
            </label>
        </div>
        @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- Name -->
    <div>
        <label class="block text-sm mb-1">Nome</label>
        <input type="text" wire:model="first_name"
            class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-black focus:border-transparent transition @error('first_name') border-red-500 @enderror"
            required>
        @error('first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @else
        <p class="text-xs text-gray-400 mt-1">Sono consentite solo lettere e il punto (.).</p>
        @enderror
    </div>

    <!-- Surname -->
    <div>
        <label class="block text-sm mb-1">Cognome</label>
        <input type="text" wire:model="last_name"
            class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-black focus:border-transparent transition @error('last_name') border-red-500 @enderror"
            required>
        @error('last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @else
        <p class="text-xs text-gray-400 mt-1">Sono consentite solo lettere e il punto (.).</p>
        @enderror
    </div>

    <!-- Email -->
    <div>
        <label class="block text-sm mb-1">E-mail</label>
        <input type="email" wire:model="email"
            class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-black focus:border-transparent transition @error('email') border-red-500 @enderror"
            required>
        @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- Password -->
    <div>
        <label class="block text-sm mb-1">Password</label>
        <div class="relative">
            <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password"
                class="w-full border px-3 py-2 rounded pr-10 focus:ring-2 focus:ring-black focus:border-transparent transition @error('password') border-red-500 @enderror"
                required>
            <button type="button" wire:click="togglePassword"
                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 transition">
                @if(!$showPassword)
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                @endif
            </button>
        </div>
        @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- Birth date -->
    <div>
        <label class="block text-sm mb-1">Data di nascita</label>
        <input type="text" wire:model="birthdate" placeholder="DD/MM/YYYY"
            class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-black focus:border-transparent transition @error('birthdate') border-red-500 @enderror">
        @error('birthdate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @else
        <p class="text-xs text-gray-400 mt-1">Opzionale</p>
        @enderror
    </div>

    <!-- Checkboxes -->
    <div class="space-y-4 text-sm">
        <label class="flex gap-3 cursor-pointer">
            <input type="checkbox" wire:model="partner_offers" class="mt-1 accent-black">
            Ricevi offerte dai nostri partner
        </label>

        <label class="flex gap-3 cursor-pointer">
            <input type="checkbox" wire:model="privacy" class="mt-1 accent-black">
            <span>
                Messaggio per la riservatezza dei dati dei clienti
                <p class="text-xs text-gray-400 mt-1">
                    The personal data you provide is used to answer queries,
                    process orders or allow access to specific information.
                </p>
            </span>
        </label>

        <label class="flex gap-3 cursor-pointer">
            <input type="checkbox" wire:model="newsletter" class="mt-1 accent-black">
            <span>
                Iscriviti alla nostra newsletter
                <p class="text-xs text-gray-400 mt-1">
                    Vuoi conoscere per primo i nuovi arrivi e le nostre offerte?
                </p>
            </span>
        </label>

        <label class="flex gap-3 font-medium cursor-pointer">
            <input type="checkbox" wire:model="terms" class="mt-1 accent-black" required>
            <span>
                Accetto le condizioni generali e la politica di riservatezza
                @error('terms') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </span>
        </label>
    </div>

    <!-- Button -->
    <button type="submit"
        class="mt-8 w-full bg-black text-white py-3 uppercase tracking-wide text-sm hover:bg-gray-800 transition disabled:opacity-50">
        <span wire:loading.remove wire:target="register">Continua</span>
        <span wire:loading wire:target="register">Registrazione in corso...</span>
    </button>

</form>
