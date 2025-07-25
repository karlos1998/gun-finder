<?php

namespace App\Console\Commands;

use App\Models\GunModel;
use App\Models\User;
use App\Providers\ListingProvider\ArmybazarProvider;
use App\Providers\ListingProvider\ListingProviderFactory;
use Illuminate\Console\Command;

class TestArmybazarProviderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:armybazar-provider {search_term? : The search term to use for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the armybazar.eu provider by fetching listings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchTerm = $this->argument('search_term') ?? 'beretta';
        $this->info("Testing armybazar.eu provider with search term: {$searchTerm}");

        // Create a test gun model with the armybazar provider
        $user = User::first();
        if (!$user) {
            $this->error('No users found in the database. Please create a user first.');
            return 1;
        }

        $gunModel = new GunModel([
            'user_id' => $user->id,
            'name' => $searchTerm,
            'provider' => 'armybazar',
        ]);

        // Get the provider
        $provider = new ArmybazarProvider();
        $this->info("Using provider: {$provider->getName()}");

        // Get the search URL
        $searchUrl = $provider->getSearchUrl($gunModel);
        $this->info("Search URL: {$searchUrl}");

        // Fetch listings
        $this->info("Fetching listings...");
        $listings = $provider->fetchListings($gunModel);
        $this->info("Fetched {$listings->count()} listings");

        // Display the listings
        if ($listings->isEmpty()) {
            $this->warn("No listings found for search term: {$searchTerm}");
            return 0;
        }

        $this->info("Displaying first 5 listings:");
        $listings->take(5)->each(function ($listing, $index) {
            $this->line("Listing " . ($index + 1) . ":");
            $this->line("  ID: {$listing['listing_id']}");
            $this->line("  Title: {$listing['title']}");
            $this->line("  Price: {$listing['price']}");
            $this->line("  URL: {$listing['url']}");
            $this->line("");
        });

        // Test fetching details for the first listing
        if ($listings->isNotEmpty()) {
            $this->info("Testing fetching details for the first listing...");
            $firstListing = $listings->first();

            // Create a temporary listing model
            $listingModel = new \App\Models\Listing([
                'listing_id' => $firstListing['listing_id'],
                'title' => $firstListing['title'],
                'description' => $firstListing['description'],
                'price' => $firstListing['price'],
                'url' => $firstListing['url'],
                'image_url' => $firstListing['image_url'],
                'region' => $firstListing['region'] ?? null,
            ]);

            // Fetch details
            $details = $provider->fetchListingDetails($listingModel);

            $this->info("Details fetched:");
            foreach ($details as $key => $value) {
                if (is_array($value)) {
                    $this->line("  {$key}: " . json_encode($value));
                } elseif ($value instanceof \DateTime) {
                    $this->line("  {$key}: " . $value->format('Y-m-d H:i:s'));
                } else {
                    $this->line("  {$key}: {$value}");
                }
            }
        }

        return 0;
    }
}
