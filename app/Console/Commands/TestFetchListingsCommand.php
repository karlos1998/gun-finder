<?php

namespace App\Console\Commands;

use App\Jobs\FetchListingsJob;
use App\Models\GunModel;
use Illuminate\Console\Command;

class TestFetchListingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:fetch-listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test fetching listings from Netgun.pl';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting test for fetching listings');

        // Get a gun model to test with
        $gunModel = GunModel::first();

        if (!$gunModel) {
            $this->error('No gun models found in the database');
            return 1;
        }

        $this->info("Testing with gun model: {$gunModel->name}");

        // Dispatch the job synchronously
        try {
            $job = new FetchListingsJob($gunModel);
            $job->handle();

            // Check if any listings were fetched
            $listings = $gunModel->listings()->get();

            $this->info("Fetched " . $listings->count() . " listings for {$gunModel->name}");

            // Display the first few listings
            foreach ($listings->take(5) as $listing) {
                $this->line("Listing ID: {$listing->listing_id}, Title: {$listing->title}, URL: {$listing->url}");
            }

            $this->info('Test completed successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error("Error during test: " . $e->getMessage());
            return 1;
        }
    }
}
