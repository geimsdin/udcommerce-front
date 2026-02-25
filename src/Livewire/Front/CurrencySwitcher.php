<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;

class CurrencySwitcher extends Component
{
    public int $selectedCurrencyId = 0;

    public $currencies;

    public function mount(): void
    {
        $this->currencies = Currency::all();

        $current = Currency::getCurrentCurrency();
        $this->selectedCurrencyId = $current->id ?? 0;
    }

    public function updatedSelectedCurrencyId(): void
    {
        $currency = Currency::find($this->selectedCurrencyId);
        if (! $currency) {
            return;
        }

        session()->put('currency', $currency);

        $cart = Cart::getCurrentCart(false, true);
        if ($cart) {
            Cart::where('id', $cart->id)->update([
                'currency_id' => $currency->id,
            ]);
        }

        $this->dispatch('updateCart');
    }

    public function render()
    {
        return view('front-ecommerce::livewire.front.currency-switcher', [
            'currencies' => $this->currencies,
        ]);
    }
}

