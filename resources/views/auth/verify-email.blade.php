<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            Przed kontynuowaniem, czy mógłbyś zweryfikować swój adres e-mail, klikając w link, który właśnie wysłaliśmy do Ciebie? Jeśli nie otrzymałeś wiadomości e-mail, chętnie wyślemy Ci kolejną.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                Nowy link weryfikacyjny został wysłany na adres e-mail podany w ustawieniach profilu.
            </div>
        @endif

        <div class="mt-6 flex flex-col space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button type="submit" class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 focus:bg-blue-700">
                        Wyślij ponownie e-mail weryfikacyjny
                    </x-button>
                </div>
            </form>

            <div class="flex justify-center space-x-4 pt-4 border-t border-gray-200">
                <a
                    href="{{ route('profile.show') }}"
                    class="text-sm text-gray-600 hover:text-blue-600 underline"
                >
                    Edytuj profil</a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <button type="submit" class="text-sm text-gray-600 hover:text-blue-600 underline">
                        Wyloguj się
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
