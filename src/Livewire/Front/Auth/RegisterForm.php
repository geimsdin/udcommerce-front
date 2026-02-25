<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;

class RegisterForm extends Component
{
    public string $title = 'sig';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $birthdate = '';

    public bool $partner_offers = false;
    public bool $privacy = false;
    public bool $newsletter = false;
    public bool $terms = false;

    public bool $showPassword = false;

    public function rules(): array
    {
        return [
            'title' => 'nullable|in:sig,sigra',
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s.]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s.]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'birthdate' => 'nullable|string|date_format:d/m/Y',
            'partner_offers' => 'boolean',
            'privacy' => 'boolean',
            'newsletter' => 'boolean',
            'terms' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'Sono consentite solo lettere e il punto (.).',
            'last_name.regex' => 'Sono consentite solo lettere e il punto (.).',
            'email.unique' => 'Questa email è già registrata.',
            'terms.accepted' => 'Devi accettare le condizioni generali.',
            'password.min' => 'La password deve avere almeno 8 caratteri.',
            'birthdate.date_format' => 'La data deve essere nel formato DD/MM/YYYY.',
        ];
    }

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => trim($this->first_name . ' ' . $this->last_name),
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Ensure the 'client' role exists before creating Client
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        $client = clone new Client();
        $client->user_id = $user->id;
        $client->first_name = $this->first_name;
        $client->last_name = $this->last_name;
        $client->save();

        Auth::login($user);

        $this->dispatch('registration-success');

        $referer = request()->header('Referer');
        if ($referer && str_ends_with(parse_url($referer, PHP_URL_PATH) ?? '', '/checkout')) {
            return redirect('/checkout/address');
        }

        return redirect()->intended($referer ?: '/');
    }

    public function render()
    {
        return view('front-ecommerce::livewire.front.auth.register-form');
    }
}
