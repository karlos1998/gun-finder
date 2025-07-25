<?php

namespace App\Jobs;

use App\Models\GunModel;
use App\Notifications\NewListingNotification;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class FetchListingsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The gun model instance.
     *
     * @var \App\Models\GunModel
     */
    protected $gunModel;

    /**
     * Create a new job instance.
     */
    public function __construct(GunModel $gunModel)
    {
        $this->gunModel = $gunModel;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get the search URL for the gun model
            $url = $this->gunModel->search_url;

            // Fetch the HTML content
            $response = Http::get($url);

            if ($response->successful()) {
                $html = $response->body();

                // Parse the HTML content
                $crawler = new Crawler($html);

                // Find all listing items
                $items = $crawler->filter('.announcements-listing-container .listing-inner .col-12 .item');

                $newListings = [];

                $detailsJobs = [];

                $items->each(function (Crawler $item) use (&$newListings, &$detailsJobs) {
                    // Extract the listing ID from the item ID attribute
                    $itemId = $item->attr('id');
                    $listingId = str_replace('ogloszenie-', '', $itemId);

                    // Check if the listing already exists
                    $existingListing = $this->gunModel->listings()->where('listing_id', $listingId)->first();

                    if ($existingListing) {
                        // If the listing was marked as deleted, mark it as not deleted
                        if ($existingListing->is_deleted) {
                            $existingListing->update(['is_deleted' => false]);
                        }
                    } else {
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

                        // Create a new listing with basic details
                        $listing = $this->gunModel->listings()->create([
                            'listing_id' => $listingId,
                            'title' => $title,
                            'description' => $description,
                            'price' => $price,
                            'url' => $url,
                            'image_url' => $imageUrl,
                        ]);

                        $newListings[] = $listing;

                        // Add a job to fetch additional details for this listing
                        $detailsJobs[] = new FetchListingDetailsJob($listing);
                    }
                });

                // Mark listings that no longer exist as deleted
                $currentListingIds = $items->each(function (Crawler $item) {
                    $itemId = $item->attr('id');
                    return str_replace('ogloszenie-', '', $itemId);
                });

                $this->gunModel->listings()
                    ->where('is_deleted', false)
                    ->whereNotIn('listing_id', $currentListingIds)
                    ->update(['is_deleted' => true]);

                // Log the number of new listings found
                if (!empty($newListings)) {
                    Log::info("Found " . count($newListings) . " new listings for {$this->gunModel->name}");

                    // Dispatch a batch of jobs to fetch details for all new listings
                    if (!empty($detailsJobs)) {
                        Bus::batch($detailsJobs)
                            ->name("fetch-details-{$this->gunModel->id}")
                            ->allowFailures()
                            ->onQueue('default')
                            ->dispatch();

                        Log::info("Dispatched batch job for fetching details for " . count($detailsJobs) . " new listings for {$this->gunModel->name}");

                        // Update the flag to indicate that the first sync is completed if needed
                        if (!$this->gunModel->first_sync_completed) {
                            Log::info("First sync completed for {$this->gunModel->name}");
                            $this->gunModel->update(['first_sync_completed' => true]);
                        }

                        // Notifications will be sent after details are fetched in FetchListingDetailsJob
                    }
                }
            } else {
                Log::error("Failed to fetch listings for {$this->gunModel->name}: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error processing {$this->gunModel->name}: " . $e->getMessage());
        }
    }
}
