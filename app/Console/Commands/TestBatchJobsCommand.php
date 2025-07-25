<?php

namespace App\Console\Commands;

use App\Jobs\FetchListingsBatchJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestBatchJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:batch-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the batch jobs for fetching listings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting test for batch jobs');

        try {
            // Dispatch the batch job
            FetchListingsBatchJob::dispatch();

            $this->info('Batch job dispatched successfully');
            $this->info('Check the logs for more information');

            return 0;
        } catch (\Exception $e) {
            $this->error("Error during test: " . $e->getMessage());
            Log::error("Error during batch jobs test: " . $e->getMessage());
            return 1;
        }
    }
}
