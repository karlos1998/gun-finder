<div>
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Gun Model Name</label>
            <div class="mt-1">
                <input type="text" wire:model="name" id="name" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="e.g. Beretta 92X Performance">
            </div>
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add Gun Model
            </button>
        </div>
    </form>
</div>
