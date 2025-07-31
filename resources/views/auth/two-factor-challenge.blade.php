<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div x-data="{ recovery: false }">
            <div class="mb-4 text-sm text-gray-600" x-show="! recovery">
                Potwierdź dostęp do swojego konta, wprowadzając kod uwierzytelniający dostarczony przez aplikację uwierzytelniającą.
            </div>

            <div class="mb-4 text-sm text-gray-600" x-cloak x-show="recovery">
                Potwierdź dostęp do swojego konta, wprowadzając jeden z kodów awaryjnego odzyskiwania.
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <div class="mt-4" x-show="! recovery">
                    <x-label for="code" value="Kod" class="text-gray-700" />
                    <x-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                </div>

                <div class="mt-4" x-cloak x-show="recovery">
                    <x-label for="recovery_code" value="Kod odzyskiwania" class="text-gray-700" />
                    <x-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                </div>

                <div class="flex flex-col space-y-4 mt-6">
                    <x-button class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 focus:bg-blue-700">
                        Zaloguj się
                    </x-button>

                    <div class="flex justify-center">
                        <button type="button" class="text-sm text-gray-600 hover:text-blue-600 underline cursor-pointer"
                                        x-show="! recovery"
                                        x-on:click="
                                            recovery = true;
                                            $nextTick(() => { $refs.recovery_code.focus() })
                                        ">
                            Użyj kodu odzyskiwania
                        </button>

                        <button type="button" class="text-sm text-gray-600 hover:text-blue-600 underline cursor-pointer"
                                        x-cloak
                                        x-show="recovery"
                                        x-on:click="
                                            recovery = false;
                                            $nextTick(() => { $refs.code.focus() })
                                        ">
                            Użyj kodu uwierzytelniającego
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
