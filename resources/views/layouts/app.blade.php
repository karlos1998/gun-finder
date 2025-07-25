<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#2563eb">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="description" content="Wyszukiwarka Broni - Śledź ogłoszenia modeli broni z Netgun.pl">

        <title>{{ config('app.name', 'Wyszukiwarka Broni') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <style>
            /* Mobile-specific styles */
            @media (max-width: 640px) {
                .max-w-7xl {
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                }

                /* Improve tap targets for mobile */
                button, a {
                    min-height: 44px;
                    min-width: 44px;
                }

                /* Improve form elements on mobile */
                input, select, textarea {
                    font-size: 16px !important; /* Prevents iOS zoom on focus */
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-slate-800" style="font-family: 'Montserrat', sans-serif;">
        <x-banner />

        <div class="min-h-screen flex flex-col">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-6 flex-grow">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                            <p class="text-sm text-gray-500">© {{ date('Y') }} Wyszukiwarka Broni. Wszystkie prawa zastrzeżone.</p>
                        </div>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-500 hover:text-gray-700">
                                <span class="sr-only">O nas</span>
                                <span>O nas</span>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-gray-700">
                                <span class="sr-only">Kontakt</span>
                                <span>Kontakt</span>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-gray-700">
                                <span class="sr-only">Polityka prywatności</span>
                                <span>Polityka prywatności</span>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
