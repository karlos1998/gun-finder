<div>
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse ($gunModels as $gunModel)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $gunModel->name }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <a href="{{ route('listings.index', $gunModel) }}" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    View Listings
                                </a>
                            </div>
                        </div>
                        <div class="mt-2 flex justify-between">
                            <div class="sm:flex">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    <p>
                                        Added on <time datetime="{{ $gunModel->created_at->format('Y-m-d') }}">{{ $gunModel->created_at->format('M d, Y') }}</time>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <button wire:click="delete({{ $gunModel->id }})" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6">
                    <p class="text-sm text-gray-500">No gun models found. Add one using the form above.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
