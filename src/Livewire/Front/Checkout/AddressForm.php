<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Checkout;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;
use Unusualdope\LaravelEcommerce\Models\CountryAddressCustomField;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;

class AddressForm extends Component
{
    public $addresses = [];

    public string $destination_name = '';

    public $selectedAddressId = null;

    public $selectedBillingAddressId = null;

    public bool $isEditing = false;

    public ?int $editingAddressId = null;

    public string $first_name = '';

    public string $last_name = '';

    public string $company = '';

    public string $vat_number = '';

    public string $address1 = '';

    public string $address2 = '';

    public string $postcode = '';

    public string $city = '';

    public string $province = '';

    public string $country = 'IT';

    public string $phone = '';

    public array $custom_fields = [];

    public $country_custom_fields_config = [];

    public bool $sameBillingAddress = true;

    public function mount()
    {
        $this->loadAddresses();
    }

    public function updatedSelectedAddressId($value)
    {
        session()->put('checkout.address_id', $value);
        session()->put('checkout.shipping_address_id', $value);
        session()->save();
    }

    public function updatedSelectedBillingAddressId($value)
    {
        session()->put('checkout.billing_address_id', $value);
        session()->save();
    }

    public function updatedCountry($value)
    {
        $this->loadCountryCustomFields($value);
    }

    protected function loadCountryCustomFields($countryCode)
    {
        $this->country_custom_fields_config = CountryAddressCustomField::forCountry($countryCode)->active()->get();

        // ensure custom_fields array has necessary keys initialized
        foreach ($this->country_custom_fields_config as $field) {
            if (!array_key_exists($field->name, $this->custom_fields)) {
                $this->custom_fields[$field->name] = null;
            }
        }
    }

    public function loadAddresses()
    {
        $user = Auth::user();
        if ($user) {
            $client = $user->client ?? Client::where('user_id', $user->id)->first();
            if ($client && method_exists($client, 'addresses')) {
                // Fetch addresses for client
                $this->addresses = $client->addresses()->get();
                $defaultAddress = $this->addresses->where('default', true)->first();
                if ($defaultAddress) {
                    $this->selectedAddressId = $defaultAddress->id;
                    $this->selectedBillingAddressId = $defaultAddress->id;
                } elseif ($this->addresses->isNotEmpty()) {
                    $this->selectedAddressId = $this->addresses->first()->id;
                    $this->selectedBillingAddressId = $this->addresses->first()->id;
                } else {
                    $this->createNewAddress();
                }

                if ($this->selectedAddressId) {
                    session()->put('checkout.address_id', $this->selectedAddressId);
                    session()->put('checkout.shipping_address_id', $this->selectedAddressId);
                    if ($this->sameBillingAddress) {
                        session()->put('checkout.billing_address_id', $this->selectedAddressId);
                    }
                    session()->save();
                }
            }
        }
    }

    public function createNewAddress()
    {
        $this->isEditing = true;
        $this->editingAddressId = null;
        $this->resetForm();
    }

    public function editAddress($id)
    {
        $address = $this->addresses->where('id', $id)->first();
        if ($address) {
            $this->isEditing = true;
            $this->editingAddressId = $id;
            $this->destination_name = $address->destination_name ?? '';
            $this->first_name = $address->first_name;
            $this->last_name = $address->last_name;
            $this->company = $address->company ?? '';
            $this->vat_number = $address->vat_number ?? '';
            $this->address1 = $address->address1;
            $this->address2 = $address->address2 ?? '';
            $this->postcode = $address->postcode;
            $this->city = $address->city;
            $this->province = $address->province ?? '';
            $this->country = $address->country ?? 'IT';
            $this->phone = $address->phone;
            $this->custom_fields = is_array($address->custom_fields) ? $address->custom_fields : [];
            $this->loadCountryCustomFields($this->country);
        }
    }

    public function deleteAddress($id)
    {
        $user = Auth::user();
        if ($user) {
            $client = $user->client ?? Client::where('user_id', $user->id)->first();
            if ($client && method_exists($client, 'addresses')) {
                $client->addresses()->where('id', $id)->delete();
                $this->selectedAddressId = null;
                $this->loadAddresses();
            }
        }
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        if (count($this->addresses) === 0) {
            return redirect('/checkout');
        }
    }

    public function resetForm()
    {
        $user = Auth::user();
        $this->destination_name = '';

        // first_name and last_name are on the clients table, not users.
        if ($user) {
            $client = $user->client ?? Client::where('user_id', $user->id)->first();

            if ($client && $client->first_name) {
                $this->first_name = $client->first_name;
                $this->last_name = $client->last_name ?? '';
            } elseif ($user->name) {
                // Fallback: split user's full name if no client record yet
                $nameParts = explode(' ', trim($user->name), 2);
                $this->first_name = $nameParts[0] ?? '';
                $this->last_name = $nameParts[1] ?? '';
            } else {
                $this->first_name = '';
                $this->last_name = '';
            }

            $this->company = $client->company_name ?? '';
            $this->phone = $client->phone ?? $user->phone ?? '';
        } else {
            $this->first_name = '';
            $this->last_name = '';
            $this->company = '';
            $this->phone = '';
        }

        $this->vat_number = '';
        $this->address1 = '';
        $this->address2 = '';
        $this->postcode = '';
        $this->city = '';
        $this->province = '';
        $this->country = 'IT';
        $this->custom_fields = [];
        $this->loadCountryCustomFields($this->country);
    }

    public function rules(): array
    {
        $rules = [
            'destination_name' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'country' => 'required|string|max:2',
            'phone' => 'required|string|max:50',
            'sameBillingAddress' => 'boolean',
        ];

        foreach ($this->country_custom_fields_config as $field) {
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($field->type === 'alphanumeric') {
                $fieldRules[] = 'alpha_num'; // Note: May need regex if space is allowed
            } else {
                $fieldRules[] = 'string';
            }

            if ($field->min_length) {
                $fieldRules[] = "min:{$field->min_length}";
            }
            if ($field->max_length) {
                $fieldRules[] = "max:{$field->max_length}";
            }

            $rules["custom_fields.{$field->name}"] = implode('|', $fieldRules);
        }

        return $rules;
    }

    public function saveAddress()
    {
        $this->validate();

        $user = Auth::user();
        if ($user) {
            $client = $user->client ?? Client::where('user_id', $user->id)->first();
            if (!$client) {
                $client = new Client;
                $client->user_id = $user->id;
                $client->first_name = $this->first_name;
                $client->last_name = $this->last_name;
                $client->save();
            }

            if (method_exists($client, 'addresses')) {
                $addressData = [
                    'destination_name' => $this->destination_name,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'company' => $this->company,
                    'vat_number' => $this->vat_number,
                    'address1' => $this->address1,
                    'address2' => $this->address2,
                    'postcode' => $this->postcode,
                    'city' => $this->city,
                    'province' => $this->province,
                    'country' => $this->country,
                    'phone' => $this->phone,
                    'custom_fields' => $this->custom_fields,
                ];

                if (count($this->addresses) === 0) {
                    $addressData['default'] = true;
                } elseif ($this->editingAddressId) {
                    $addressData['default'] = $this->addresses->where('id', $this->editingAddressId)->first()->default;
                } else {
                    $addressData['default'] = false;
                }

                if ($this->editingAddressId) {
                    $client->addresses()->where('id', $this->editingAddressId)->update($addressData);
                } else {
                    $newAddress = $client->addresses()->create($addressData);
                    $this->selectedAddressId = $newAddress->id;
                }

                session()->put('checkout.address_id', $this->selectedAddressId);
                session()->save();


                $defaultAddress = $client->addresses()->where('default', true)->first();
                if ($defaultAddress && ($defaultAddress->id === $this->editingAddressId || count($this->addresses) == 0)) {
                    $client->address = $this->address1 . ($this->address2 ? ', ' . $this->address2 : '');
                    $client->postcode = $this->postcode;
                    $client->city = $this->city;
                    $client->state = $this->province;
                    $client->country = $this->country;
                    $client->phone = $this->phone;
                    $client->company_name = $this->company;
                    $client->vat_code = $this->vat_number;
                    $client->save();
                }

                $this->isEditing = false;
                $this->loadAddresses();
            }
        } else {
            // Guest address handling
            $addressData = [
                'destination_name' => $this->destination_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'company' => $this->company,
                'vat_number' => $this->vat_number,
                'address1' => $this->address1,
                'address2' => $this->address2,
                'postcode' => $this->postcode,
                'city' => $this->city,
                'province' => $this->province,
                'country' => $this->country,
                'phone' => $this->phone,
                'custom_fields' => $this->custom_fields,
                'client_id' => null,
                'default' => false,
            ];

            $newAddress = \Unusualdope\LaravelEcommerce\Models\Address::create($addressData);
            $this->selectedAddressId = $newAddress->id;
            session()->put('checkout.address_id', $this->selectedAddressId);
            session()->save();

            $this->isEditing = false;
        }
    }

    public function submitAddress()
    {
        if ($this->isEditing) {
            $this->saveAddress();
        }

        if (!$this->selectedAddressId) {
            $this->addError('selectedAddressId', __('front-ecommerce::checkout.validation.select_shipping'));

            return;
        }

        if (!$this->sameBillingAddress && !$this->selectedBillingAddressId) {
            $this->addError('selectedBillingAddressId', __('front-ecommerce::checkout.validation.select_billing'));

            return;
        }

        $address = \Unusualdope\LaravelEcommerce\Models\Address::find($this->selectedAddressId);
        if ($address) {
            session()->put('checkout.address_id', $address->id);
            session()->put('checkout.shipping_address_id', $address->id);
            session()->put('checkout.shipping_address', $address->toArray());

            if ($this->sameBillingAddress) {
                session()->put('checkout.billing_address_id', $address->id);
                session()->put('checkout.billing_address', $address->toArray());
            } else {
                $billingAddress = \Unusualdope\LaravelEcommerce\Models\Address::find($this->selectedBillingAddressId);
                if ($billingAddress) {
                    session()->put('checkout.billing_address_id', $billingAddress->id);
                    session()->put('checkout.billing_address', $billingAddress->toArray());
                }
            }

            session()->save();

        }

        return redirect('/shipping');
    }

    public function render()
    {
        $countries = [
            'IT' => 'Italia',
            'DE' => 'Germania',
            'FR' => 'Francia',
            'ES' => 'Spagna',
            'GB' => 'Regno Unito',
            'AT' => 'Austria',
            'BE' => 'Belgio',
            'NL' => 'Paesi Bassi',
            'CH' => 'Svizzera',
            'PT' => 'Portogallo',
            'PL' => 'Polonia',
            'GR' => 'Grecia',
            'SE' => 'Svezia',
            'DK' => 'Danimarca',
            'NO' => 'Norvegia',
            'FI' => 'Finlandia',
            'IE' => 'Irlanda',
            'CZ' => 'Repubblica Ceca',
            'RO' => 'Romania',
            'HU' => 'Ungheria',
            'US' => 'United States',
        ];

        return view('front-ecommerce::livewire.front.checkout.address-form', [
            'countries' => $countries,
        ]);
    }
}
