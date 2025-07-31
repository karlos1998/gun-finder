<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Zapomniałeś hasła? Nie ma problemu. Podaj nam swój adres e-mail, a wyślemy Ci link do resetowania hasła, który pozwoli Ci wybrać nowe.') }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="Email" class="text-gray-700" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-center mt-6">
                <x-button class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 focus:bg-blue-700">
                    Wyślij link resetujący hasło
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
