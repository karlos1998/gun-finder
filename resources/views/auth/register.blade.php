<x-guest-layout>
    <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg border border-gray-200">
        <div class="flex justify-center mb-6">
            <svg class="h-12 w-auto text-gray-900" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 7L21.8 6.6C21.4 5.6 20.3 5 19.2 5H15V4C15 3.4 14.6 3 14 3H10C9.4 3 9 3.4 9 4V5H4.8C3.7 5 2.6 5.6 2.2 6.6L2 7C1.4 7 1 7.4 1 8V9C1 9.6 1.4 10 2 10V16C2 17.1 2.9 18 4 18H5V19C5 20.1 5.9 21 7 21H17C18.1 21 19 20.1 19 19V18H20C21.1 18 22 17.1 22 16V10C22.6 10 23 9.6 23 9V8C23 7.4 22.6 7 22 7ZM4 16V10H5V16H4ZM17 19H7V11C7 10.4 7.4 10 8 10H16C16.6 10 17 10.4 17 11V19ZM20 16H19V11C19 9.3 17.7 8 16 8H8C6.3 8 5 9.3 5 11V16H4V8H20V16ZM21 9H3V8H3.2C3.6 7.4 4.3 7 5 7H19C19.7 7 20.4 7.4 20.8 8H21V9Z" fill="currentColor"/>
            </svg>
        </div>

        <h1 class="text-center text-2xl font-bold mb-2 text-gray-900">Wyszukiwarka Broni</h1>
        <h2 class="text-center text-lg font-medium mb-6 text-gray-600">Utwórz nowe konto</h2>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="Imię i nazwisko" class="text-gray-700" />
                <x-input id="name" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring focus:ring-black focus:ring-opacity-20" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="Email" class="text-gray-700" />
                <x-input id="email" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring focus:ring-black focus:ring-opacity-20" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Hasło" class="text-gray-700" />
                <x-input id="password" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring focus:ring-black focus:ring-opacity-20" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="Potwierdź hasło" class="text-gray-700" />
                <x-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring focus:ring-black focus:ring-opacity-20" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required class="text-black focus:ring-black" />

                            <div class="ms-2">
                                {!! __('Akceptuję :terms_of_service oraz :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-sm font-medium text-black hover:text-gray-700">'.__('Regulamin').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-sm font-medium text-black hover:text-gray-700">'.__('Politykę Prywatności').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="mt-6">
                <x-button class="w-full justify-center py-3 bg-black hover:bg-gray-800 focus:bg-gray-800">
                    Zarejestruj się
                </x-button>
            </div>

            <div class="flex items-center justify-center mt-6 pt-4 border-t border-gray-200">
                <span class="text-sm text-gray-600">Masz już konto?</span>
                <a class="ml-1 text-sm font-medium text-black hover:text-gray-700" href="{{ route('login') }}">
                    Zaloguj się
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
