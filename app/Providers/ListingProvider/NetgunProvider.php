<?php

namespace App\Providers\ListingProvider;

use App\Models\GunModel;
use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class NetgunProvider implements ListingProviderInterface
{
    /**
     * Get the provider name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'netgun';
    }

    /**
     * Get the search URL for the gun model.
     *
     * @param GunModel $gunModel
     * @return string
     */
    public function getSearchUrl(GunModel $gunModel): string
    {
        $query = str_replace(' ', '+', $gunModel->name);
        return "https://www.netgun.pl/wyszukiwanie?query={$query}";
    }

    /**
     * Fetch listings for the gun model.
     *
     * @param GunModel $gunModel
     * @return Collection
     */
    public function fetchListings(GunModel $gunModel): Collection
    {
        $baseUrl = $this->getSearchUrl($gunModel);
        $currentUrl = $baseUrl;
        $currentPage = 1;
        $hasNextPage = true;
        $allListings = collect();

        // Process all pages until there's no "Next" button
        while ($hasNextPage) {
            Log::info("Fetching page {$currentPage} for {$gunModel->name} from URL: {$currentUrl}");

            // Fetch the HTML content
            $response = Http::get($currentUrl);

            if (!$response->successful()) {
                Log::error("Failed to fetch listings for {$gunModel->name} on page {$currentPage}: " . $response->status());
                break;
            }

            $html = $response->body();

            // Parse the HTML content
            $crawler = new Crawler($html);

            // Find all listing items
            $items = $crawler->filter('.announcements-listing-container .listing-inner .col-12 .item');

            $items->each(function (Crawler $item) use (&$allListings) {
                // Extract the listing ID from the item ID attribute
                $itemId = $item->attr('id');
                $listingId = str_replace('ogloszenie-', '', $itemId);

                // Extract the basic listing details
                $title = $item->filter('.title h3')->text();
                $description = $item->filter('.description p')->count() > 0
                    ? $item->filter('.description p')->text()
                    : null;

                $price = $item->filter('.price span')->count() > 0
                    ? $item->filter('.price span')->last()->text()
                    : null;

                $url = $item->filter('a')->attr('href');

                $imageUrl = $item->filter('.thumb img')->count() > 0
                    ? $item->filter('.thumb img')->attr('src')
                    : null;

                // Add the listing to the collection
                $allListings->push([
                    'listing_id' => $listingId,
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'url' => $url,
                    'image_url' => $imageUrl,
                    'provider' => $this->getName(),
                ]);
            });

            // Check if there's a next page
            $nextPageLink = $crawler->filter('.pagination-container .pagination .page-item a[rel="next"]');
            if ($nextPageLink->count() > 0) {
                $currentUrl = $nextPageLink->attr('href');
                $currentPage++;
            } else {
                $hasNextPage = false;
                Log::info("Reached the last page ({$currentPage}) for {$gunModel->name}");
            }
        }

        return $allListings;
    }

    /**
     * Fetch details for a listing.
     *
     * @param Listing $listing
     * @return array
     */
    public function fetchListingDetails(Listing $listing): array
    {
        $url = $listing->url;
        $response = Http::get($url);

        if (!$response->successful()) {
            Log::error("Failed to fetch details for listing {$listing->id}: " . $response->status());
            return [];
        }

        $html = $response->body();
        $crawler = new Crawler($html);

        // Extract phone number
        $phone = null;
        $phoneElement = $crawler->filter('.info div:contains("Telefon:") a');
        if ($phoneElement->count() > 0) {
            $phone = $phoneElement->text();
        }

        // Extract location
        $city = null;
        $region = null;
        $locationElement = $crawler->filter('.info div:contains("Lokalizacja:") span');
        if ($locationElement->count() > 0) {
            $location = $locationElement->text();
            $locationParts = explode(', ', $location);
            if (count($locationParts) > 1) {
                $city = $locationParts[0];
                $region = $locationParts[1];
            } else {
                $city = $location;
            }
        }

        // Extract condition
        $condition = null;
        $conditionElement = $crawler->filter('.info div:contains("Stan:") span');
        if ($conditionElement->count() > 0) {
            $condition = $conditionElement->text();
        }

        // Extract listing date
        $listingDate = null;
        $dateElement = $crawler->filter('.date span strong:last-child');
        if ($dateElement->count() > 0) {
            $dateText = $dateElement->text();
            try {
                $listingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dateText);
            } catch (\Exception $e) {
                // If date parsing fails, just leave it as null
                Log::warning("Failed to parse date for listing {$listing->id}: {$e->getMessage()}");
            }
        }

        // Extract gallery images
        $galleryImages = [];
        $galleryElements = $crawler->filter('.images.pswp-thumbnails div a');
        $galleryElements->each(function (Crawler $element) use (&$galleryImages) {
            $href = $element->attr('href');
            if ($href) {
                $galleryImages[] = $href;
            }
        });

        return [
            'phone_number' => $phone,
            'city' => $city,
            'region' => $region,
            'condition' => $condition,
            'listing_date' => $listingDate,
            'gallery_images' => !empty($galleryImages) ? $galleryImages : null,
        ];
    }
}
