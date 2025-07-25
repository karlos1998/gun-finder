<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('gun-models.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Add New Gun Model') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Your Gun Models') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Manage your gun models and view listings for each model.') }}
                        </p>
                    </div>

                    <livewire:gun-model-list />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
