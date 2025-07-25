<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate max-w-xl">
                {{ $listing->title }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('listings.index', $gunModel) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Powrót do ogłoszeń
                </a>
                <a href="{{ $listing->url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Zobacz na Netgun
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-4 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Main Image and Gallery -->
                    <div>
                        <div class="mb-4 rounded-lg overflow-hidden shadow-md">
                            @if ($listing->image_url)
                                <img src="{{ $listing->full_image_url }}" alt="{{ $listing->title }}" class="w-full h-auto object-contain bg-gray-50">
                            @else
                                <div class="w-full h-64 bg-gray-100 flex items-center justify-center rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        @if (!empty($listing->gallery_images))
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach ($listing->full_gallery_images as $image)
                                    <a href="{{ $image }}" data-lightbox="gallery" class="block rounded-md overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        <img src="{{ $image }}" alt="{{ $listing->title }}" class="w-full h-24 object-cover hover:opacity-90 transition-opacity">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Listing Details -->
                    <div class="flex flex-col h-full">
                        <div class="mb-6">
                            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $listing->title }}</h1>
                                @if ($listing->is_deleted)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Usunięte
                                    </span>
                                @endif
                            </div>
                            <p class="text-2xl font-semibold text-blue-600">{{ $listing->price }}</p>
                        </div>

                        <div class="border-t border-gray-200 py-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if ($listing->condition)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Stan
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $listing->condition }}</p>
                                    </div>
                                @endif

                                @if ($listing->listing_date)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Data ogłoszenia
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $listing->listing_date->format('d.m.Y') }}</p>
                                    </div>
                                @endif

                                @if ($listing->city || $listing->region)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Lokalizacja
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-900 font-medium">
                                            @if ($listing->city && $listing->region)
                                                {{ $listing->city }}, {{ $listing->region }}
                                            @elseif ($listing->city)
                                                {{ $listing->city }}
                                            @else
                                                {{ $listing->region }}
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if ($listing->phone_number)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            Telefon
                                        </h3>
                                        <p class="mt-1 text-sm font-medium">
                                            <a href="tel:{{ $listing->phone_number }}" class="text-blue-600 hover:text-blue-800 transition">
                                                {{ $listing->phone_number }}
                                            </a>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-gray-200 py-4 flex-grow">
                            <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                                Opis
                            </h3>
                            <div class="prose max-w-none text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">
                                {{ $listing->description }}
                            </div>
                        </div>

                        <div class="mt-auto pt-4 border-t border-gray-200">
                            <a href="{{ $listing->url }}" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Zobacz pełne ogłoszenie na Netgun.pl
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
