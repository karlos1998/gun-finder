<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gun Finder CRM</h1>
            </div>

            <h2 class="text-center text-xl font-semibold mb-6 text-gray-800 dark:text-gray-200">{{ __('Sign in to your account') }}</h2>

            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-button class="w-full justify-center py-2 px-4">
                        {{ __('Log in') }}
                    </x-button>
                </div>

                <div class="flex items-center justify-center mt-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("Don't have an account?") }}</span>
                    <a class="ml-1 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" href="{{ route('register') }}">
                        {{ __('Sign up') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
