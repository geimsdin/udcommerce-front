<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Carrier;

class ShippingForm extends Component
{
    public $carriers = [];

    public $selectedCarrierId = null;

    public $notes = '';

    public function mount()
    {
        $this->carriers = Carrier::where('active', true)->orderBy('position')->get();

        // Restore from session if available
        $savedMethod = session('checkout.shipping_method');
        if ($savedMethod) {
            $this->selectedCarrierId = $savedMethod['id'] ?? null;
            $this->notes = $savedMethod['notes'] ?? '';
        } elseif ($this->carriers->isNotEmpty()) {
            $this->selectedCarrierId = $this->carriers->first()->id;
        }
    }

    public function updatedSelectedCarrierId($value)
    {
        $carrier = Carrier::find($value);
        if ($carrier) {
            session()->put('checkout.shipping_method', [
                'id' => $carrier->id,
                'name' => $carrier->name,
                'price' => $carrier->price,
                'notes' => $this->notes,
            ]);
            session()->save();

            $this->dispatch('updateCart');
        }
    }

    public function submit()
    {
        if (! $this->selectedCarrierId) {
            $this->addError('selectedCarrierId', 'Seleziona un metodo di spedizione per continuare.');

            return;
        }

        $carrier = Carrier::find($this->selectedCarrierId);
        if (! $carrier) {
            $this->addError('selectedCarrierId', 'Metodo di spedizione non valido.');

            return;
        }

        session()->put('checkout.shipping_method', [
            'id' => $carrier->id,
            'name' => $carrier->name,
            'price' => $carrier->price,
            'notes' => $this->notes,
        ]);

        session()->save();

        return redirect('/payment');
    }

    public function render()
    {
        return view('front-ecommerce::livewire.front.checkout.shipping-form');
    }
}
