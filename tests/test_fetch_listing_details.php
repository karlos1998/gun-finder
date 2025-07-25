<?php

use App\Jobs\FetchListingDetailsJob;
use App\Models\Listing;
use Illuminate\Support\Facades\Log;

// Set up logging
Log::info('Starting test script for fetching listing details');

// Get a listing to test with
$listing = Listing::where('id', '>', 60)->where('id', '<', 81)->first();

if (!$listing) {
    Log::error('No listings found in the database with IDs between 61 and 80');
    echo "No listings found in the database with IDs between 61 and 80\n";
    exit(1);
}

Log::info("Testing with listing: {$listing->id} - {$listing->title}");
echo "Testing with listing: {$listing->id} - {$listing->title}\n";

// Dispatch the job synchronously
try {
    $job = new FetchListingDetailsJob($listing);
    $job->handle();

    // Check if the listing details were fetched
    $listing->refresh();

    Log::info("Listing details: Phone: {$listing->phone_number}, City: {$listing->city}, Region: {$listing->region}");
    echo "Listing details: Phone: {$listing->phone_number}, City: {$listing->city}, Region: {$listing->region}\n";

    if ($listing->gallery_images) {
        Log::info("Gallery images: " . count($listing->gallery_images));
        echo "Gallery images: " . count($listing->gallery_images) . "\n";
    } else {
        Log::info("No gallery images found");
        echo "No gallery images found\n";
    }

    Log::info('Test completed successfully');
    echo "Test completed successfully\n";
} catch (\Exception $e) {
    Log::error("Error during test: " . $e->getMessage());
    echo "Error during test: " . $e->getMessage() . "\n";
    exit(1);
}
