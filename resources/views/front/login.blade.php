@extends('front-ecommerce::front.layouts.app')

@section('title', __('Login'))

@section('content')
    <div class="min-h-[60vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <h1 class="text-3xl font-semibold text-center mb-8">{{ __('Accedi al tuo account') }}</h1>

            <livewire:front-ecommerce.auth.login-form />
        </div>
    </div>
@endsection