<div>
    {{-- Status Messages --}}
    @if(session('status'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg text-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        {{-- Email Field --}}
        <div>
            <label for="email" class="block text-sm text-gray-700 mb-2">
                {{ __('E-mail') }}
            </label>
            <input type="email" wire:model="email" id="email"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent transition @error('email') border-red-500 @enderror"
                autofocus autocomplete="email">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Field --}}
        <div>
            <label for="password" class="block text-sm text-gray-700 mb-2">
                {{ __('Password') }}
            </label>
            <div class="relative">
                <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" id="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent transition pr-12 @error('password') border-red-500 @enderror"
                    autocomplete="current-password">
                <button type="button" wire:click="togglePassword"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    @if($showPassword)
                        {{-- Eye open icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    @else
                        {{-- Eye closed icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    @endif
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
            <input type="checkbox" wire:model="remember" id="remember"
                class="w-4 h-4 border-gray-300 rounded text-black focus:ring-black">
            <label for="remember" class="ml-2 text-sm text-gray-600">
                {{ __('Ricordami') }}
            </label>
        </div>

        {{-- Forgot Password Link --}}
        @if (Route::has('password.request'))
            <div class="text-center">
                <a href="{{ route('password.request') }}"
                    class="text-sm text-gray-600 hover:text-black hover:underline transition">
                    {{ __('Hai dimenticato la password?') }}
                </a>
            </div>
        @endif

        {{-- Cloudflare Turnstile --}}
        @if(config('services.turnstile.site_key'))
            <div class="flex justify-center">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
            </div>
        @endif

        {{-- Submit Button --}}
        <button type="submit"
            class="w-full bg-black text-white py-3.5 rounded-lg font-medium hover:bg-gray-800 transition uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Login') }}</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                {{ __('Accesso in corso...') }}
            </span>
        </button>
    </form>

    {{-- Social Login Buttons --}}
    @if($socialProviders->isNotEmpty())
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">{{ __('Oppure accedi con') }}</span>
                </div>
            </div>

            <div class="mt-4 grid gap-3 {{ $socialProviders->count() > 2 ? 'grid-cols-2' : 'grid-cols-' . $socialProviders->count() }}">
                @foreach($socialProviders as $sp)
                    @php $key = $sp->provider; @endphp
                    <a href="{{ route('social.redirect', $key) }}"
                        class="flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-gray-700"
                        id="social-login-{{ $key }}">
                        @if(!empty($sp->icon_path))
                            <img src="{{ Storage::url($sp->icon_path) }}" class="w-5 h-5 object-contain" alt="{{ $key }}">
                            <span>{{ $key === 'twitter' ? 'X (Twitter)' : ucfirst($key) }}</span>
                        @else
                            @switch($key)
                                @case('facebook')
                                    <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    <span>Facebook</span>
                                @break
                                @case('google')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                    <span>Google</span>
                                @break
                                @case('twitter')
                                    <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    <span>X (Twitter)</span>
                                @break
                                @case('instagram')
                                    <svg class="w-5 h-5 text-[#E4405F]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                                    <span>Instagram</span>
                                @break
                            @endswitch
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Register Link --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            {{ __('Non hai un account?') }}
            <a href="/account/register" class="font-medium text-black hover:underline transition" wire:navigate>
                {{ __('Creane uno qui') }}
            </a>
        </p>
    </div>
</div>