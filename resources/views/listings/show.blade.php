<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $listing->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('listings.index', $gunModel) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    {{ __('Back to Listings') }}
                </a>
                <a href="{{ $listing->url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('View on Netgun') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Main Image and Gallery -->
                    <div>
                        <div class="mb-4">
                            @if ($listing->image_url)
                                <img src="{{ $listing->full_image_url }}" alt="{{ $listing->title }}" class="w-full h-auto rounded-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-400">{{ __('No image') }}</span>
                                </div>
                            @endif
                        </div>

                        @if (!empty($listing->gallery_images))
                            <div class="grid grid-cols-4 gap-2">
                                @foreach ($listing->full_gallery_images as $image)
                                    <a href="{{ $image }}" data-lightbox="gallery" class="block">
                                        <img src="{{ $image }}" alt="{{ $listing->title }}" class="w-full h-24 object-cover rounded">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Listing Details -->
                    <div>
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $listing->title }}</h1>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-semibold text-indigo-600">{{ $listing->price }}</p>
                                @if ($listing->is_deleted)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ __('Deleted') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-gray-200 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if ($listing->condition)
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">{{ __('Condition') }}</h3>
                                        <p class="mt-1 text-sm text-gray-900">{{ $listing->condition }}</p>
                                    </div>
                                @endif

                                @if ($listing->listing_date)
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">{{ __('Listed On') }}</h3>
                                        <p class="mt-1 text-sm text-gray-900">{{ $listing->listing_date->format('M d, Y') }}</p>
                                    </div>
                                @endif

                                @if ($listing->city || $listing->region)
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">{{ __('Location') }}</h3>
                                        <p class="mt-1 text-sm text-gray-900">
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
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">{{ __('Phone') }}</h3>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <a href="tel:{{ $listing->phone_number }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $listing->phone_number }}
                                            </a>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-gray-200 py-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Description') }}</h3>
                            <div class="prose max-w-none text-sm text-gray-900">
                                {{ $listing->description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
