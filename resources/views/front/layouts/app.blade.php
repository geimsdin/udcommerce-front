<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Front Ecommerce')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('structured-data')
</head>

<body>
    @include('front-ecommerce::front.layouts.partials.header')

    @include('front-ecommerce::front.layouts.partials.breadcrumb')

    <main id="main" class="container mx-auto">
        @yield('content')
    </main>

    @include('front-ecommerce::front.layouts.partials.footer')

    @stack('scripts')
    @livewireScripts
</body>

</html>