<?php

use App\Jobs\FetchListingsJob;
use App\Models\GunModel;
use Illuminate\Support\Facades\Log;

// Set up logging
Log::info('Starting test script for fetching listings');

// Get a gun model to test with
$gunModel = GunModel::first();

if (!$gunModel) {
    Log::error('No gun models found in the database');
    echo "No gun models found in the database\n";
    exit(1);
}

Log::info("Testing with gun model: {$gunModel->name}");
echo "Testing with gun model: {$gunModel->name}\n";

// Dispatch the job synchronously
try {
    $job = new FetchListingsJob($gunModel);
    $job->handle();

    // Check if any listings were fetched
    $listings = $gunModel->listings()->get();

    Log::info("Fetched " . $listings->count() . " listings for {$gunModel->name}");
    echo "Fetched " . $listings->count() . " listings for {$gunModel->name}\n";

    // Display the first few listings
    foreach ($listings->take(5) as $listing) {
        echo "Listing ID: {$listing->listing_id}, Title: {$listing->title}, URL: {$listing->url}\n";
    }

    Log::info('Test completed successfully');
    echo "Test completed successfully\n";
} catch (\Exception $e) {
    Log::error("Error during test: " . $e->getMessage());
    echo "Error during test: " . $e->getMessage() . "\n";
    exit(1);
}
