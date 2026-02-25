@extends('front-ecommerce::front.layouts.app')

@section('title', $product->name ?? __('front-ecommerce::products.product'))
@section('content')
    <livewire:front-ecommerce.catalog.product-miniature :product="$product" />
@endsection
