<?php

namespace App\Jobs;

use App\Models\GunModel;
use App\Models\Listing;
use App\Notifications\NewListingNotification;
use App\Providers\ListingProvider\ListingProviderFactory;
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
            $allNewListings = [];
            $allDetailsJobs = [];
            $allCurrentListingIds = [];

            // Get all available providers
            $providers = ListingProviderFactory::getAllProviders();

            // Fetch listings from all providers
            foreach ($providers as $providerName => $provider) {
                Log::info("Using provider {$provider->getName()} for {$this->gunModel->name}");

                // Fetch listings using the provider
                $fetchedListings = $provider->fetchListings($this->gunModel);

                Log::info("Fetched " . $fetchedListings->count() . " listings for {$this->gunModel->name} from {$provider->getName()}");

                $newListings = [];
                $detailsJobs = [];

                foreach ($fetchedListings as $fetchedListing) {
                    $listingId = $fetchedListing['listing_id'];
                    $allCurrentListingIds[] = $listingId;

                    // Check if the listing exists in the database
                    $existingListing = Listing::where('listing_id', $listingId)->first();

                    // Check if this gun model is already associated with the listing
                    $isAssociated = false;
                    if ($existingListing) {
                        $isAssociated = $existingListing->gunModels()->where('gun_model_id', $this->gunModel->id)->exists();

                        if (!$isAssociated) {
                            // If the listing exists but is not associated with this gun model, associate it
                            Log::info("Associating existing listing {$listingId} with gun model {$this->gunModel->name}");
                        }
                    }

                    if ($existingListing) {
                        // If the listing was marked as deleted, mark it as not deleted
                        if ($existingListing->is_deleted) {
                            $existingListing->update(['is_deleted' => false]);
                        }

                        // If the listing is not associated with this gun model, associate it
                        if (!$isAssociated) {
                            $this->gunModel->listings()->attach($existingListing->id);
                        }
                    } else {
                        // Create a new listing with basic details
                        $listing = Listing::create([
                            'listing_id' => $listingId,
                            'title' => $fetchedListing['title'],
                            'description' => $fetchedListing['description'],
                            'price' => $fetchedListing['price'],
                            'url' => $fetchedListing['url'],
                            'image_url' => $fetchedListing['image_url'],
                            'provider' => $fetchedListing['provider'],
                            'region' => $fetchedListing['region'] ?? null,
                        ]);

                        // Associate the listing with this gun model
                        $this->gunModel->listings()->attach($listing->id);

                        $newListings[] = $listing;

                        // Add a job to fetch additional details for this listing
                        $detailsJobs[] = new FetchListingDetailsJob($listing);
                    }
                }

                // Add to our collections
                $allNewListings = array_merge($allNewListings, $newListings);
                $allDetailsJobs = array_merge($allDetailsJobs, $detailsJobs);
            }

            // Get all listings associated with this gun model
            $associatedListings = $this->gunModel->listings()
                ->where('is_deleted', false)
                ->whereNotIn('listings.listing_id', $allCurrentListingIds)
                ->get();

            // Mark listings that no longer exist as deleted
            foreach ($associatedListings as $listing) {
                $listing->update(['is_deleted' => true]);
            }

            // Log the number of new listings found
            if (!empty($allNewListings)) {
                Log::info("Found " . count($allNewListings) . " new listings for {$this->gunModel->name}");

                // Dispatch a batch of jobs to fetch details for all new listings
                if (!empty($allDetailsJobs)) {
                    Bus::batch($allDetailsJobs)
                        ->name("fetch-details-{$this->gunModel->id}")
                        ->allowFailures()
                        ->onQueue('default')
                        ->dispatch();

                    Log::info("Dispatched batch job for fetching details for " . count($allDetailsJobs) . " new listings for {$this->gunModel->name}");

                    // Update the flag to indicate that the first sync is completed if needed
                    if (!$this->gunModel->first_sync_completed) {
                        Log::info("First sync completed for {$this->gunModel->name}");
                        $this->gunModel->update(['first_sync_completed' => true]);
                    }

                    // Notifications will be sent after details are fetched in FetchListingDetailsJob
                }
            } else {
                Log::info("No new listings found for {$this->gunModel->name}");
            }
        } catch (\Exception $e) {
            Log::error("Error processing {$this->gunModel->name}: " . $e->getMessage());
        }
    }
}
