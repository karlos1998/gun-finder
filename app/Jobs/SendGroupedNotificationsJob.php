<?php

namespace App\Jobs;

use App\Models\GunModel;
use App\Models\Listing;
use App\Models\User;
use App\Notifications\NewListingsGroupNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SendGroupedNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The collection of new listings.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $newListings;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $newListings)
    {
        $this->newListings = $newListings;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Group listings by gun model
            $listingsByGunModel = [];

            foreach ($this->newListings as $listing) {
                foreach ($listing->gunModels as $gunModel) {
                    if ($gunModel->first_sync_completed) {
                        if (!isset($listingsByGunModel[$gunModel->id])) {
                            $listingsByGunModel[$gunModel->id] = [
                                'gun_model' => $gunModel,
                                'listings' => collect(),
                            ];
                        }

                        $listingsByGunModel[$gunModel->id]['listings']->push($listing);
                    }
                }
            }

            // Send notifications for each gun model
            foreach ($listingsByGunModel as $gunModelData) {
                $gunModel = $gunModelData['gun_model'];
                $listings = $gunModelData['listings'];

                $user = $gunModel->user;
                if ($user) {
                    Log::info("Sending grouped notification for gun model {$gunModel->name} to user {$user->id} with {$listings->count()} listings");
                    $user->notify(new NewListingsGroupNotification($gunModel, $listings));
                }
            }

            Log::info("Sent grouped notifications for " . count($listingsByGunModel) . " gun models");
        } catch (\Exception $e) {
            Log::error("Error sending grouped notifications: " . $e->getMessage());
        }
    }
}
