<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\SocialAuthProvider;

class LoginForm extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public bool $showPassword = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'email.required' => 'L\'indirizzo email è obbligatorio.',
        'email.email' => 'Inserisci un indirizzo email valido.',
        'password.required' => 'La password è obbligatoria.',
        'password.min' => 'La password deve avere almeno 6 caratteri.',
    ];

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login()
    {
        $this->validate();

        // Rate limiting
        $throttleKey = Str::transliterate(Str::lower($this->email) . '|' . request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('Troppi tentativi. Riprova tra :seconds secondi.', [
                    'seconds' => $seconds,
                ]),
            ]);
        }

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('Le credenziali inserite non sono corrette.'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        session()->regenerate();

        // Dispatch browser event for success feedback
        $this->dispatch('login-success');

        $referer = request()->header('Referer');
        if ($referer && str_ends_with(parse_url($referer, PHP_URL_PATH) ?? '', '/checkout')) {
            return redirect('/checkout/address');
        }

        // Redirect to intended page or account
        return redirect()->intended('/account');
    }

    public function render()
    {
        $socialProviders = SocialAuthProvider::getActiveProviders()
            ->filter(fn(SocialAuthProvider $p) => $p->isConfigured());

        return view('front-ecommerce::livewire.front.auth.login-form', [
            'socialProviders' => $socialProviders,
            'providerMeta' => SocialAuthProvider::PROVIDERS,
        ]);
    }
}
