<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (!$gunModel->first_sync_completed)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Trwa synchronizacja ogłoszeń dla tego modelu broni. Prosimy odświeżyć stronę za kilka minut.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-4 sm:p-6">
                @if ($listings->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500 text-lg">Nie znaleziono ogłoszeń dla tego modelu broni.</p>
                        <p class="text-gray-500 mt-2">Ogłoszenia będą automatycznie pobierane co 30 minut.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Znaleziono {{ $listings->total() }} ogłoszeń
                                @if ($listings->hasPages())
                                    (strona {{ $listings->currentPage() }} z {{ $listings->lastPage() }})
                                @endif
                            </h3>
                            <div class="flex space-x-2 bg-gray-100 p-1 rounded-lg">
                                <button wire:click="setViewMode('grid')" class="p-2 rounded hover:bg-blue-100 active:bg-blue-200 transition {{ $viewMode === 'grid' ? 'bg-blue-200' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </button>
                                <button wire:click="setViewMode('list')" class="p-2 rounded hover:bg-blue-100 active:bg-blue-200 transition {{ $viewMode === 'list' ? 'bg-blue-200' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Filters and Sorting -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <!-- Region Filter -->
                                <div class="w-full md:w-1/5">
                                    <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Województwo</label>
                                    <select id="region" wire:model.live="region" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Wszystkie województwa</option>
                                        @foreach($regions as $regionOption)
                                            <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Provider Filter -->
                                <div class="w-full md:w-1/5">
                                    <label for="provider" class="block text-sm font-medium text-gray-700 mb-1">Serwis</label>
                                    <select id="provider" wire:model.live="provider" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Wszystkie serwisy</option>
                                        @foreach($providers as $providerOption)
                                            <option value="{{ $providerOption }}">{{ $providerOption }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Price Range Filters -->
                                <div class="w-full md:w-1/4">
                                    <label for="minPrice" class="block text-sm font-medium text-gray-700 mb-1">Cena od</label>
                                    <input type="number" id="minPrice" wire:model.live="minPrice" placeholder="Min cena" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div class="w-full md:w-1/4">
                                    <label for="maxPrice" class="block text-sm font-medium text-gray-700 mb-1">Cena do</label>
                                    <input type="number" id="maxPrice" wire:model.live="maxPrice" placeholder="Max cena" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <!-- Sort Direction -->
                                <div class="w-full md:w-1/4">
                                    <label for="sortBy" class="block text-sm font-medium text-gray-700 mb-1">Sortowanie</label>
                                    <select id="sortBy" wire:model.live="sortBy" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="latest">Najnowsze</option>
                                        <option value="price_asc">Cena: od najniższej</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Reset Filters Button -->
                            <div class="flex justify-end">
                                <button wire:click="resetFilters" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Resetuj filtry
                                </button>
                            </div>
                        </div>

                        <div id="listings-container" class="grid grid-cols-1 {{ $viewMode === 'grid' ? 'md:grid-cols-2 lg:grid-cols-3' : '' }} gap-6">
                            @foreach ($listings as $listing)
                                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 {{ $listing->is_deleted ? 'bg-red-50' : 'bg-white' }}">
                                    <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="block">
                                        <div class="aspect-w-16 aspect-h-9 relative">
                                            @if ($listing->image_url)
                                                <img src="{{ $listing->full_image_url }}" alt="{{ $listing->title }}" class="object-cover w-full h-48 hover:opacity-90 transition-opacity">
                                            @else
                                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
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
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    {{ $listing->price }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start">
                                            <h3 class="text-lg font-semibold truncate flex-1">
                                                <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="hover:text-blue-600 transition">
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

                                            @if ($listing->provider)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                    </svg>
                                                    {{ $listing->provider }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3 text-sm text-gray-700 line-clamp-2">
                                            {{ $listing->description }}
                                        </div>

                                        <div class="mt-4 flex justify-between items-center">
                                            <a href="{{ route('listings.show', [$gunModel, $listing]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Zobacz szczegóły
                                            </a>
                                            <a href="{{ $listing->url }}" target="_blank" class="text-gray-500 hover:text-blue-600 transition p-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($listings->hasPages())
                            <div class="mt-6">
                                {{ $listings->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
