@extends('front-ecommerce::front.layouts.app')

{{-- @section('title', $product->name ?? __('Product')) --}}
@section('title', 'Product')
@section('content')
    <div id="account" class="container mx-auto">
        <style>
            .account-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .account-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            }
        </style>
        <section id="account-section" class="m-20 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto py-8">

                <!-- Section Title -->
                <h1 class="text-3xl font-semibold text-gray-900 text-center mb-10 italic">
                    Il tuo account
                </h1>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <!-- Card 1: INFORMAZIONI -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            Informazioni
                        </span>
                    </a>

                    <!-- Card 2: INDIRIZZI -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            Indirizzi
                        </span>
                    </a>

                    <!-- Card 3: CRONOLOGIA ORDINI E DETTAGLI -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            Cronologia Ordini e Dettagli
                        </span>
                    </a>

                    <!-- Card 4: NOTA DI CREDITO -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            Nota di Credito
                        </span>
                    </a>

                    <!-- Card 5: BUONI SCONTO -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            Buoni Sconto
                        </span>
                    </a>

                    <!-- Card 6: RESO MERCE -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            Reso Merce
                        </span>
                    </a>

                    <!-- Card 7: LA MIA LISTA DEI DESIDERI -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="currentColor" stroke="currentColor"
                            viewBox="0 0 24 24" stroke-width="0">
                            <path
                                d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            La Mia Lista dei Desideri
                        </span>
                    </a>

                    <!-- Card 8: GIFT CARDS -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            Gift Cards
                        </span>
                    </a>

                    <!-- Card 9: I MIEI AVVISI -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            I Miei Avvisi
                        </span>
                    </a>

                    <!-- Card 10: I MIEI DATI PERSONALI -->
                    <a href="#"
                        class="account-card flex flex-col items-center justify-center p-8 bg-[#f9fafb] border border-gray-200 rounded-lg cursor-pointer">
                        <svg class="w-8 h-8 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide text-center">
                            I Miei Dati Personali
                        </span>
                    </a>

                </div>

            </div>
        </section>
    </div>
@endsection
