<x-guest-layout>
    <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg border border-gray-200">
        <div class="flex justify-center mb-6">
            <svg class="h-12 w-auto text-gray-900" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 7L21.8 6.6C21.4 5.6 20.3 5 19.2 5H15V4C15 3.4 14.6 3 14 3H10C9.4 3 9 3.4 9 4V5H4.8C3.7 5 2.6 5.6 2.2 6.6L2 7C1.4 7 1 7.4 1 8V9C1 9.6 1.4 10 2 10V16C2 17.1 2.9 18 4 18H5V19C5 20.1 5.9 21 7 21H17C18.1 21 19 20.1 19 19V18H20C21.1 18 22 17.1 22 16V10C22.6 10 23 9.6 23 9V8C23 7.4 22.6 7 22 7ZM4 16V10H5V16H4ZM17 19H7V11C7 10.4 7.4 10 8 10H16C16.6 10 17 10.4 17 11V19ZM20 16H19V11C19 9.3 17.7 8 16 8H8C6.3 8 5 9.3 5 11V16H4V8H20V16ZM21 9H3V8H3.2C3.6 7.4 4.3 7 5 7H19C19.7 7 20.4 7.4 20.8 8H21V9Z" fill="currentColor"/>
            </svg>
        </div>

        <h1 class="text-center text-2xl font-bold mb-2 text-gray-900">Wyszukiwarka Broni</h1>
        <h2 class="text-center text-lg font-medium mb-6 text-gray-600">Zaloguj się do swojego konta</h2>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="Email" class="text-gray-700" />
                <x-input id="email" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring focus:ring-black focus:ring-opacity-20" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Hasło" class="text-gray-700" />
                <x-input id="password" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring focus:ring-black focus:ring-opacity-20" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" class="text-black focus:ring-black" />
                    <span class="ms-2 text-sm text-gray-600">Zapamiętaj mnie</span>
                </label>
            </div>

            <div class="flex flex-col space-y-4 mt-6">
                <x-button class="w-full justify-center py-3 bg-black hover:bg-gray-800 focus:bg-gray-800">
                    Zaloguj się
                </x-button>

                @if (Route::has('password.request'))
                    <a class="text-sm text-center text-gray-600 hover:text-black" href="{{ route('password.request') }}">
                        Zapomniałeś hasła?
                    </a>
                @endif
            </div>

            <div class="flex items-center justify-center mt-6 pt-4 border-t border-gray-200">
                <span class="text-sm text-gray-600">Nie masz jeszcze konta?</span>
                <a class="ml-1 text-sm font-medium text-black hover:text-gray-700" href="{{ route('register') }}">
                    Zarejestruj się
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
