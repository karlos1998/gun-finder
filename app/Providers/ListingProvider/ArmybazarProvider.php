<?php

namespace App\Providers\ListingProvider;

use App\Models\GunModel;
use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class ArmybazarProvider implements ListingProviderInterface
{
    /**
     * Get the provider name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'armybazar';
    }

    /**
     * Get the search URL for the gun model.
     *
     * @param GunModel $gunModel
     * @return string
     */
    public function getSearchUrl(GunModel $gunModel): string
    {
        return "http://www.armybazar.eu/pl/wyszukiwanie/";
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
        $currentPage = 1;
        $hasNextPage = true;
        $allListings = collect();

        // Process up to 10 pages
        while ($hasNextPage && $currentPage <= 10) {
            $url = $currentPage === 1
                ? $baseUrl
                : $baseUrl . "strona/{$currentPage}/";

            Log::info("Fetching page {$currentPage} for {$gunModel->name} from URL: {$url}");

            // Prepare form data
            $formData = [
                'search_slovo' => $gunModel->name,
                'search_typ' => '0',
                'search_cena_od' => '0',
                'search_cena_do' => '',
                'search' => 'Szukaj'
            ];

            // Fetch the HTML content with POST request
            $response = Http::asForm()->post($url, $formData);

            if (!$response->successful()) {
                Log::error("Failed to fetch listings for {$gunModel->name} on page {$currentPage}: " . $response->status());
                break;
            }

            $html = $response->body();

            // Save HTML to file for debugging
//            $debugDir = storage_path('app/debug');
//            if (!file_exists($debugDir)) {
//                mkdir($debugDir, 0755, true);
//            }
//            $filename = $debugDir . '/armybazar_' . $gunModel->name . '_page' . $currentPage . '_' . date('Y-m-d_H-i-s') . '.html';
//            file_put_contents($filename, $html);
//            Log::info("Saved HTML response to {$filename}");

            // Remove XML declaration and DOCTYPE before parsing
            $html = preg_replace('/^<\?xml.*?\?>.*?<!DOCTYPE.*?>/s', '', $html);

            // Parse the HTML content
            $crawler = new Crawler($html);

            // Find all listing items
            $items = $crawler->filter('div.inner.inzerat');

            // Log the number of items found
            Log::info("Found {$items->count()} listing items on page {$currentPage} for {$gunModel->name}");

            // Debug the HTML structure
            Log::info("HTML structure: " . substr($html, 0, 1000));

            // Debug the first item to see its structure
            if ($items->count() > 0) {
                $firstItem = $items->first();
                Log::info("First item HTML: " . $firstItem->outerHtml());
                Log::info("a[class=\"img\"] count: " . $firstItem->filter('a[class="img"]')->count());
                Log::info("div.top h2 a count: " . $firstItem->filter('div.top h2 a')->count());
                Log::info("ul.cendat li.cena strong count: " . $firstItem->filter('ul.cendat li.cena strong')->count());
                Log::info("ul.cendat li.lokalita count: " . $firstItem->filter('ul.cendat li.lokalita')->count());
            }

            // Filter out the top item
            $items = $items->reduce(function (Crawler $node, $i) {
                return !$node->matches('.top');
            });
            Log::info("Found {$items->count()} listing items after filtering");

            $items->each(function (Crawler $item, $i) use (&$allListings) {
                // Skip items that don't have the expected structure
                // Use more specific selectors to match the HTML structure
                $imgLinkCount = $item->filter('a[class="img"]')->count();
                $titleLinkCount = $item->filter('div.top h2 a')->count();

                if ($imgLinkCount === 0 || $titleLinkCount === 0) {
                    Log::warning("Skipping item {$i} because it doesn't have the expected structure");
                    Log::warning("a[class=\"img\"] count: " . $imgLinkCount);
                    Log::warning("div.top h2 a count: " . $titleLinkCount);
                    return;
                }

                // Extract the listing ID from the URL
                $url = $item->filter('div.top h2 a')->attr('href');
                Log::info("Listing URL: {$url}");

                $listingId = null;
                if (preg_match('/id(\d+)/', $url, $matches)) {
                    $listingId = $matches[1];
                    Log::info("Extracted listing ID: {$listingId}");
                } else {
                    Log::warning("Failed to extract listing ID from URL: {$url}");
                }

                if (!$listingId) {
                    return;
                }

                // Extract the basic listing details
                $title = $item->filter('div.top h2 a')->text();
                $description = $item->filter('p')->count() > 0
                    ? $item->filter('p')->text()
                    : null;

                $price = null;
                $priceElement = $item->filter('ul.cendat li.cena strong');
                if ($priceElement->count() > 0) {
                    $price = $priceElement->text();
                }

                $imageUrl = null;
                $imageElement = $item->filter('a[class="img"] img');
                if ($imageElement->count() > 0) {
                    $imageUrl = $imageElement->attr('src');
                }

                // Extract region
                $region = null;
                $regionElement = $item->filter('ul.cendat li.lokalita');
                if ($regionElement->count() > 0) {
                    $region = $regionElement->text();
                }

                // Add the listing to the collection
                $allListings->push([
                    'listing_id' => $listingId,
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'url' => $url,
                    'image_url' => $imageUrl,
                    'region' => $region,
                    'provider' => $this->getName(),
                ]);
            });

            // Check if there's a next page
            $paginationDiv = $crawler->filter('#strankovanie');
            Log::info("Pagination div found: " . ($paginationDiv->count() > 0 ? 'Yes' : 'No'));

            if ($paginationDiv->count() > 0) {
                // Log all links in the pagination div
                $paginationLinks = $paginationDiv->filter('a');
                Log::info("Found {$paginationLinks->count()} pagination links");

                $nextPageLink = $paginationDiv->filter('a[rel="next"]');
                Log::info("Next page link found: " . ($nextPageLink->count() > 0 ? 'Yes' : 'No'));

                if ($nextPageLink->count() > 0) {
                    $currentPage++;
                    Log::info("Moving to page {$currentPage}");
                } else {
                    $hasNextPage = false;
                    Log::info("Reached the last page ({$currentPage}) for {$gunModel->name}");
                }
            } else {
                $hasNextPage = false;
                Log::info("No pagination found for {$gunModel->name}");
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
        $phoneElement = $crawler->filter('p:contains("Telefon:")');
        if ($phoneElement->count() > 0) {
            $phoneText = $phoneElement->text();
            if (preg_match('/Telefon:\s*([0-9\s-]+)/', $phoneText, $matches)) {
                $phone = trim($matches[1]);
            }
        }

        // Extract location (region is already extracted from the listing page)
        $city = null;
        $region = $listing->region;

        // Extract condition
        $condition = null;

        // Extract listing date
        $listingDate = null;
        $dateElement = $crawler->filter('.cendat .datum');
        if ($dateElement->count() > 0) {
            $dateText = $dateElement->text();
            try {
                // Format: DD.MM.YYYY\nHH:MM
                $dateText = preg_replace('/\s+/', ' ', $dateText);
                $listingDate = \Carbon\Carbon::createFromFormat('d.m.Y H:i', $dateText);
            } catch (\Exception $e) {
                // If date parsing fails, just leave it as null
                Log::warning("Failed to parse date for listing {$listing->id}: {$e->getMessage()}");
            }
        }

        // Extract gallery images
        $galleryImages = [];

        // Log the HTML of the gallery section for debugging
        $gallerySection = $crawler->filter('#inz_foto');
        if ($gallerySection->count() > 0) {
            Log::info("Gallery section found for listing {$listing->id}");
            Log::info("Gallery section HTML: " . $gallerySection->outerHtml());
        } else {
            Log::warning("Gallery section not found for listing {$listing->id}");
        }

        // Extract all links with class 'fancy' inside #inz_foto
        $allGalleryLinks = $crawler->filter('#inz_foto a.fancy');
        Log::info("Found {$allGalleryLinks->count()} gallery links for listing {$listing->id}");

        // Extract the main image
        $mainImage = $crawler->filter('#inz_foto a.fancy.bigimg');
        if ($mainImage->count() > 0) {
            $href = $mainImage->attr('href');
            Log::info("Main image found: {$href}");
            $galleryImages[] = $href;
        } else {
            Log::warning("Main image not found for listing {$listing->id}");
        }

        // Extract additional images
        $additionalImages = $crawler->filter('#inz_foto a.fancy.img');
        Log::info("Found {$additionalImages->count()} additional images for listing {$listing->id}");

        $additionalImages->each(function (Crawler $element, $i) use (&$galleryImages) {
            $href = $element->attr('href');
            if ($href) {
                Log::info("Additional image {$i} found: {$href}");
                $galleryImages[] = $href;
            }
        });

        // If no images were found with the specific classes, try a more general approach
        if (empty($galleryImages)) {
            Log::warning("No images found with specific classes, trying a more general approach");

            // Try to get all links in the gallery section that have href attributes ending with image extensions
            $allLinks = $crawler->filter('#inz_foto a[href$=".jpg"], #inz_foto a[href$=".jpeg"], #inz_foto a[href$=".png"], #inz_foto a[href$=".gif"]');
            Log::info("Found {$allLinks->count()} links with image extensions");

            $allLinks->each(function (Crawler $element, $i) use (&$galleryImages) {
                $href = $element->attr('href');
                if ($href) {
                    Log::info("Image link {$i} found: {$href}");
                    $galleryImages[] = $href;
                }
            });
        }

        Log::info("Total gallery images found: " . count($galleryImages));

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
