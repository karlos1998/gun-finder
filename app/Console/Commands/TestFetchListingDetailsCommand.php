<?php

namespace App\Console\Commands;

use App\Jobs\FetchListingDetailsJob;
use App\Models\Listing;
use Illuminate\Console\Command;

class TestFetchListingDetailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:fetch-listing-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test fetching listing details from Netgun.pl';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting test for fetching listing details');

        // Get a listing to test with
        $listing = Listing::where('id', '>', 60)->where('id', '<', 81)->first();

        if (!$listing) {
            $this->error('No listings found in the database with IDs between 61 and 80');
            return 1;
        }

        $this->info("Testing with listing: {$listing->id} - {$listing->title}");

        // Dispatch the job synchronously
        try {
            $job = new FetchListingDetailsJob($listing);
            $job->handle();

            // Check if the listing details were fetched
            $listing->refresh();

            $this->info("Listing details: Phone: {$listing->phone_number}, City: {$listing->city}, Region: {$listing->region}");

            if ($listing->gallery_images) {
                $this->info("Gallery images: " . count($listing->gallery_images));
            } else {
                $this->info("No gallery images found");
            }

            $this->info('Test completed successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error("Error during test: " . $e->getMessage());
            return 1;
        }
    }
}
