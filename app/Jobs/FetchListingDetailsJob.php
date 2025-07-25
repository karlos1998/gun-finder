<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Notifications\NewListingNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;

class FetchListingDetailsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Maximum number of retries
     */
    protected $maxRetries = 3;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Fetch the listing details page
            $url = $this->listing->url;

            // Use a cache key to prevent hammering the server with requests
            $cacheKey = 'listing_details_' . $this->listing->id;

            // Try to get the response with retries
            $response = $this->getWithRetries($url);

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

                // Send notification to the user if this is not the first sync
                $gunModel = $this->listing->gunModel;
                if ($gunModel && $gunModel->first_sync_completed) {
                    $user = $gunModel->user;
                    if ($user) {
                        $user->notify(new NewListingNotification($this->listing));
                        Log::info("Sent notification for listing {$this->listing->id} to user {$user->id}");
                    }
                }
            } else {
                Log::error("Failed to fetch details for listing {$this->listing->id}: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error processing listing {$this->listing->id}: " . $e->getMessage());
        }
    }

    /**
     * Get a URL with retries and exponential backoff
     *
     * @param string $url
     * @return \Illuminate\Http\Client\Response
     */
    protected function getWithRetries(string $url)
    {
        $attempt = 0;
        $response = null;
        $exception = null;

        while ($attempt < $this->maxRetries) {
            try {
                // Add a delay with exponential backoff (except for the first attempt)
                if ($attempt > 0) {
                    $delay = pow(2, $attempt) * 1000; // milliseconds
                    usleep($delay * 1000); // convert to microseconds
                    Log::info("Retry {$attempt} for listing {$this->listing->id} after {$delay}ms delay");
                }

                $response = Http::timeout(30)->get($url);

                // If successful, return the response
                if ($response->successful()) {
                    return $response;
                }

                // If we got a response but it's not successful, log it and continue retrying
                Log::warning("Attempt {$attempt} failed for listing {$this->listing->id}: " . $response->status());
            } catch (\Exception $e) {
                // If an exception occurred, log it and continue retrying
                $exception = $e;
                Log::warning("Exception during attempt {$attempt} for listing {$this->listing->id}: " . $e->getMessage());
            }

            $attempt++;
        }

        // If we've exhausted all retries, return the last response or throw the last exception
        if ($response) {
            return $response;
        }

        throw $exception ?? new \Exception("Failed to fetch details after {$this->maxRetries} attempts");
    }
}
