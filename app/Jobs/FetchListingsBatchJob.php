<?php

namespace App\Jobs;

use App\Models\GunModel;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class FetchListingsBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get all gun models
            $gunModels = GunModel::all();

            if ($gunModels->isEmpty()) {
                Log::warning('No gun models found in the database.');
                return;
            }

            // Create a batch of jobs for each gun model
            $jobs = $gunModels->map(function ($gunModel) {
                return new FetchListingsJob($gunModel);
            })->toArray();

            // Dispatch the batch
            Bus::batch($jobs)
                ->name('fetch-listings')
                ->allowFailures()
                ->onQueue('default')
                ->dispatch();

            Log::info('Dispatched batch job for fetching listings for ' . $gunModels->count() . ' gun models.');
        } catch (\Exception $e) {
            Log::error('Error dispatching batch job for fetching listings: ' . $e->getMessage());
        }
    }
}
