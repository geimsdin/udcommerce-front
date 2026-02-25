<?php

namespace Unusualdope\FrontLaravelEcommerce\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends ContentController
{
    protected function render(Request $request, array $params = []): mixed
    {
        if (!Auth::check()) {
            return redirect('/login?redirect=' . urlencode($request->fullUrl()));
        }

        $user = Auth::user();

        $addresses = $this->getUserAddresses($user);

        $this->setBreadcrumb();

        return view('front-ecommerce::front.checkout.address', [
            'breadcrumb' => static::$breadcrumb,
            'user' => $user,
            'addresses' => $addresses,
            'countries' => $this->getCountries(),
        ]);
    }

    /**
     * Get user's saved addresses.
     */
    protected function getUserAddresses($user): array
    {
        if (method_exists($user, 'addresses')) {
            return $user->addresses()->get()->toArray();
        }

        $addressData = [];

        if (isset($user->address) || isset($user->shipping_address)) {
            $addressData[] = [
                'id' => 1,
                'type' => 'shipping',
                'alias' => 'Indirizzo di spedizione',
                'first_name' => $user->first_name ?? $user->name ?? '',
                'last_name' => $user->last_name ?? '',
                'company' => $user->company ?? '',
                'address1' => $user->address ?? $user->shipping_address ?? '',
                'address2' => $user->address2 ?? '',
                'city' => $user->city ?? '',
                'postcode' => $user->postcode ?? $user->zip ?? '',
                'country' => $user->country ?? 'IT',
                'phone' => $user->phone ?? '',
                'is_default' => true,
            ];
        }

        return $addressData;
    }

    /**
     * Get list of countries for dropdown.
     */
    protected function getCountries(): array
    {
        return [
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
        ];
    }

    protected function setBreadcrumb(): void
    {
        static::$breadcrumb = [
            'Home' => '/',
            'Checkout' => '/checkout',
            'Indirizzi' => '',
        ];
    }
}
