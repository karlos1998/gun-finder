<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Notifications\NewListingNotification;
use App\Providers\ListingProvider\ListingProviderFactory;
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
            // Get the provider for this listing
            $provider = $this->listing->getProvider();
            Log::info("Using provider {$provider->getName()} for listing {$this->listing->id}");

            // Fetch the listing details using the provider
            $details = $provider->fetchListingDetails($this->listing);

            if (!empty($details)) {
                // Update the listing with the additional details
                $this->listing->update($details);

                Log::info("Updated details for listing {$this->listing->id}");

                // Send notification to users if this is not the first sync
                foreach ($this->listing->gunModels as $gunModel) {
                    if ($gunModel->first_sync_completed) {
                        $user = $gunModel->user;
                        if ($user) {
                            $user->notify(new NewListingNotification($this->listing));
                            Log::info("Sent notification for listing {$this->listing->id} to user {$user->id}");
                        }
                    }
                }
            } else {
                Log::error("Failed to fetch details for listing {$this->listing->id}");
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
