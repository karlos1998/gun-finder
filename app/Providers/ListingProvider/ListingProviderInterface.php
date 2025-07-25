<?php

namespace App\Providers\ListingProvider;

use App\Models\GunModel;
use App\Models\Listing;
use Illuminate\Support\Collection;

interface ListingProviderInterface
{
    /**
     * Get the provider name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the search URL for the gun model.
     *
     * @param GunModel $gunModel
     * @return string
     */
    public function getSearchUrl(GunModel $gunModel): string;

    /**
     * Fetch listings for the gun model.
     *
     * @param GunModel $gunModel
     * @return Collection
     */
    public function fetchListings(GunModel $gunModel): Collection;

    /**
     * Fetch details for a listing.
     *
     * @param Listing $listing
     * @return array
     */
    public function fetchListingDetails(Listing $listing): array;
}
