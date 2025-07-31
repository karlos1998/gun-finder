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
                background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
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
