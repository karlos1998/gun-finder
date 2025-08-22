<?php

namespace App\Livewire;

use App\Models\GunModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class GunModelListings extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $gunModelId;
    public $viewMode = 'grid';
    public $region = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $sortBy = 'latest';
    public $provider = '';

    protected $queryString = ['viewMode', 'region', 'minPrice', 'maxPrice', 'sortBy', 'provider'];

    public function mount($gunModelId)
    {
        $this->gunModelId = $gunModelId;

        // Set the view mode from localStorage if available
        if (session()->has('viewMode')) {
            $this->viewMode = session('viewMode');
        }
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        session(['viewMode' => $mode]);
    }

    public function resetFilters()
    {
        $this->region = '';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->provider = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function updatedRegion()
    {
        $this->resetPage();
    }

    public function updatedMinPrice()
    {
        $this->resetPage();
    }

    public function updatedMaxPrice()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedProvider()
    {
        $this->resetPage();
    }

    public function render()
    {
        $gunModel = GunModel::findOrFail($this->gunModelId);

        // Check if the gun model belongs to the authenticated user
        if ($gunModel->user_id !== Auth::id()) {
            abort(403);
        }

        $query = $gunModel->listings();

        // Apply region filter
        if (!empty($this->region)) {
            $query->where('region', $this->region);
        }

        // Apply provider filter
        if (!empty($this->provider)) {
            $query->where('provider', $this->provider);
        }

        // Apply price range filters
        if (!empty($this->minPrice)) {
            $query->where(function($q) {
                $q->whereRaw("CAST(REPLACE(REPLACE(price, ' zł', ''), ',', '.') AS DECIMAL(10,2)) >= ?", [(float)$this->minPrice]);
            });
        }

        if (!empty($this->maxPrice)) {
            $query->where(function($q) {
                $q->whereRaw("CAST(REPLACE(REPLACE(price, ' zł', ''), ',', '.') AS DECIMAL(10,2)) <= ?", [(float)$this->maxPrice]);
            });
        }

        // Apply sorting
        if ($this->sortBy === 'price_asc') {
            $query->orderByRaw("CAST(REPLACE(REPLACE(price, ' zł', ''), ',', '.') AS DECIMAL(10,2)) ASC");
        } else {
            $query->orderBy('listing_date', 'desc');
        }

        $listings = $query->paginate(100);

        // Get unique regions for the filter dropdown
        $regions = $gunModel->listings()
            ->whereNotNull('region')
            ->distinct()
            ->pluck('region')
            ->sort()
            ->values();

        // Get unique providers for the filter dropdown
        $providers = $gunModel->listings()
            ->whereNotNull('provider')
            ->distinct()
            ->pluck('provider')
            ->sort()
            ->values();

        return view('livewire.gun-model-listings', [
            'gunModel' => $gunModel,
            'listings' => $listings,
            'regions' => $regions,
            'providers' => $providers,
        ]);
    }
}
