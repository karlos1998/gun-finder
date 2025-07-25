<?php

namespace App\Jobs;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class FetchListingDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The listing instance.
     *
     * @var \App\Models\Listing
     */
    protected $listing;

    /**
     * Create a new job instance.
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Fetch the listing details page
            $url = $this->listing->url;
            // Ensure the URL has a leading slash
            $url = !str_starts_with($url, '/') ? '/' . $url : $url;
            $detailsUrl = 'https://netgun.pl' . $url;
            $response = Http::get($detailsUrl);

            if ($response->successful()) {
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
                        Log::warning("Failed to parse date for listing {$this->listing->id}: {$e->getMessage()}");
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

                // Update the listing with the additional details
                $this->listing->update([
                    'phone_number' => $phone,
                    'city' => $city,
                    'region' => $region,
                    'condition' => $condition,
                    'listing_date' => $listingDate,
                    'gallery_images' => !empty($galleryImages) ? $galleryImages : null,
                ]);

                Log::info("Updated details for listing {$this->listing->id}");
            } else {
                Log::error("Failed to fetch details for listing {$this->listing->id}: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error processing listing {$this->listing->id}: " . $e->getMessage());
        }
    }
}
