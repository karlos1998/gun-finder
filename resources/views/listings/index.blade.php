<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ogłoszenia dla {{ $gunModel->name }}
            </h2>
            <a href="{{ $gunModel->search_url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Zobacz na Netgun
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if ($listings->isEmpty())
                    <div class="text-center">
                        <p class="text-gray-500">Nie znaleziono ogłoszeń dla tego modelu broni.</p>
                        <p class="text-gray-500 mt-2">Ogłoszenia będą automatycznie pobierane co 30 minut.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Znaleziono {{ $listings->count() }} ogłoszeń</h3>
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
                                <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-200 {{ $listing->is_deleted ? 'bg-red-50' : 'bg-white' }}">
                                    <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="block">
                                        <div class="aspect-w-16 aspect-h-9 relative">
                                            @if ($listing->image_url)
                                                <img src="{{ $listing->full_image_url }}" alt="{{ $listing->title }}" class="object-cover w-full h-48 hover:opacity-90 transition-opacity">
                                            @else
                                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-400">Brak zdjęcia</span>
                                                </div>
                                            @endif

                                            @if ($listing->is_deleted)
                                                <div class="absolute top-2 right-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Usunięte
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="absolute bottom-0 right-0 m-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $listing->price }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start">
                                            <h3 class="text-lg font-semibold truncate flex-1">
                                                <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="hover:text-indigo-600">
                                                    {{ $listing->title }}
                                                </a>
                                            </h3>
                                        </div>

                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @if ($listing->city || $listing->region)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                                </span>
                                            @endif

                                            @if ($listing->condition)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $listing->condition }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3 text-sm text-gray-700 line-clamp-2">
                                            {{ $listing->description }}
                                        </div>

                                        <div class="mt-4 flex justify-between items-center">
                                            <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Zobacz szczegóły
                                            </a>
                                            <a href="{{ $listing->url }}" target="_blank" class="text-gray-500 hover:text-gray-700 text-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
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
