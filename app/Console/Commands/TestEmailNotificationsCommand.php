<?php

namespace App\Console\Commands;

use App\Models\GunModel;
use App\Models\Listing;
use App\Models\User;
use App\Notifications\NewListingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestEmailNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notifications {user_id} {gun_model_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the email notifications for new listings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $gunModelId = $this->argument('gun_model_id');

        $this->info("Starting test for email notifications with user_id: {$userId}");

        try {
            // Get the user
            $user = User::findOrFail($userId);

            // Get a gun model
            if ($gunModelId) {
                $gunModel = GunModel::findOrFail($gunModelId);
            } else {
                $gunModel = $user->gunModels()->first();

                if (!$gunModel) {
                    $this->error("No gun models found for user {$userId}");
                    return 1;
                }
            }

            $this->info("Using gun model: {$gunModel->name}");

            // Create a test listing
            $listing = new Listing([
                'gun_model_id' => $gunModel->id,
                'listing_id' => 'test-' . time(),
                'title' => 'Test Listing for Email Notification',
                'description' => 'This is a test listing to verify that email notifications are working correctly.',
                'price' => '1000 zł',
                'url' => 'https://www.netgun.pl/test-listing',
                'image_url' => '/images/announcement_thumb.png',
            ]);

            $listing->save();

            $this->info("Created test listing: {$listing->title}");

            // Send a notification
            $user->notify(new NewListingNotification($listing));

            $this->info("Sent email notification to {$user->email}");
            $this->info("Check your email to verify that the notification was received");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error during test: " . $e->getMessage());
            Log::error("Error during email notifications test: " . $e->getMessage());
            return 1;
        }
    }
}
