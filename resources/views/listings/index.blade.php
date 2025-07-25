<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Listings for') }} {{ $gunModel->name }}
            </h2>
            <a href="{{ $gunModel->search_url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('View on Netgun') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if ($listings->isEmpty())
                    <div class="text-center">
                        <p class="text-gray-500">{{ __('No listings found for this gun model.') }}</p>
                        <p class="text-gray-500 mt-2">{{ __('Listings will be automatically fetched every 30 minutes.') }}</p>
                    </div>
                @else
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Found') }} {{ $listings->count() }} {{ __('listings') }}</h3>
                            <div class="flex space-x-2">
                                <button id="grid-view" class="p-2 bg-gray-200 rounded hover:bg-gray-300 active:bg-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </button>
                                <button id="list-view" class="p-2 bg-gray-200 rounded hover:bg-gray-300 active:bg-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="listings-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($listings as $listing)
                                <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 {{ $listing->is_deleted ? 'bg-red-50' : 'bg-white' }}">
                                    <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="block">
                                        <div class="aspect-w-16 aspect-h-9 relative">
                                            @if ($listing->image_url)
                                                <img src="{{ $listing->full_image_url }}" alt="{{ $listing->title }}" class="object-cover w-full h-48">
                                            @else
                                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-400">{{ __('No image') }}</span>
                                                </div>
                                            @endif

                                            @if ($listing->is_deleted)
                                                <div class="absolute top-2 right-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ __('Deleted') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start">
                                            <h3 class="text-lg font-semibold truncate flex-1">
                                                <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="hover:text-indigo-600">
                                                    {{ $listing->title }}
                                                </a>
                                            </h3>
                                            <p class="text-lg font-bold text-indigo-600 ml-2">{{ $listing->price }}</p>
                                        </div>

                                        @if ($listing->city || $listing->region)
                                            <p class="text-sm text-gray-500 mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                @if ($listing->city && $listing->region)
                                                    {{ $listing->city }}, {{ $listing->region }}
                                                @elseif ($listing->city)
                                                    {{ $listing->city }}
                                                @else
                                                    {{ $listing->region }}
                                                @endif
                                            </p>
                                        @endif

                                        @if ($listing->condition)
                                            <p class="text-sm text-gray-500 mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $listing->condition }}
                                            </p>
                                        @endif

                                        <div class="mt-2 text-sm text-gray-700 line-clamp-2">
                                            {{ $listing->description }}
                                        </div>

                                        <div class="mt-4 flex justify-between items-center">
                                            <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                {{ __('View Details') }}
                                            </a>
                                            <a href="{{ $listing->url }}" target="_blank" class="text-gray-500 hover:text-gray-700 text-sm">
                                                {{ __('View on Netgun') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const container = document.getElementById('listings-container');
                                const gridViewBtn = document.getElementById('grid-view');
                                const listViewBtn = document.getElementById('list-view');

                                gridViewBtn.addEventListener('click', function() {
                                    container.classList.remove('grid-cols-1');
                                    container.classList.add('grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3');
                                    gridViewBtn.classList.add('bg-gray-300');
                                    listViewBtn.classList.remove('bg-gray-300');
                                });

                                listViewBtn.addEventListener('click', function() {
                                    container.classList.remove('md:grid-cols-2', 'lg:grid-cols-3');
                                    container.classList.add('grid-cols-1');
                                    listViewBtn.classList.add('bg-gray-300');
                                    gridViewBtn.classList.remove('bg-gray-300');
                                });

                                // Set default view
                                gridViewBtn.classList.add('bg-gray-300');
                            });
                        </script>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
