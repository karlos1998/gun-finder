<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            To jest bezpieczny obszar aplikacji. Proszę potwierdzić swoje hasło przed kontynuowaniem.
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="Hasło" class="text-gray-700" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex items-center justify-center mt-6">
                <x-button class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 focus:bg-blue-700">
                    Potwierdź
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
