<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#000000">

        <title>{{ config('app.name', 'Wyszukiwarka Broni') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <style>
            body {
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415L51.8 0h2.827zM5.373 0l-.83.828L5.96 2.243 8.2 0H5.374zM48.97 0l3.657 3.657-1.414 1.414L46.143 0h2.828zM11.03 0L7.372 3.657 8.787 5.07 13.857 0H11.03zm32.284 0L49.8 6.485 48.384 7.9l-7.9-7.9h2.83zM16.686 0L10.2 6.485 11.616 7.9l7.9-7.9h-2.83zm20.97 0l9.315 9.314-1.414 1.414L34.828 0h2.83zM22.344 0L13.03 9.314l1.414 1.414L25.172 0h-2.83zM32 0l12.142 12.142-1.414 1.414L30 .828 17.272 13.556l-1.414-1.414L28 0h4zM.284 0l28 28-1.414 1.414L0 2.544v2.83L25.456 30l-1.414 1.414-28-28L0 0h.284zM0 5.373l25.456 25.455-1.414 1.415L0 8.2v2.83l21.627 21.628-1.414 1.414L0 13.657v2.828l17.8 17.8-1.414 1.414L0 19.1v2.83l14.142 14.14-1.414 1.415L0 24.544V60h60V0H0v5.374z' fill='%23f1f1f1' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
                background-color: #111;
            }
        </style>
    </head>
    <body class="font-sans antialiased" style="font-family: 'Montserrat', sans-serif;">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 sm:px-6 lg:px-8">
            {{ $slot }}

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">© {{ date('Y') }} Wyszukiwarka Broni. Wszystkie prawa zastrzeżone.</p>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
