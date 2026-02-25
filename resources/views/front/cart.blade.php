@extends('front-ecommerce::front.layouts.app')

@section('title', __('Cart'))

@section('content')
<section id="cart" class="w-full px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-20">

        <!-- LEFT: CART ITEMS -->
        <livewire:front-ecommerce.cart.cart-items />

        <!-- RIGHT: SUMMARY -->
        <livewire:front-ecommerce.cart.cart-summary />

    </div>
</section>

@endsection
