<?php

namespace App\Console\Commands;

use App\Models\GunModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestAutoDispatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:auto-dispatch {user_id} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the automatic dispatch of jobs when a new gun model is added';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $name = $this->argument('name');

        $this->info("Starting test for automatic dispatch with user_id: {$userId} and name: {$name}");

        try {
            // Create a new gun model
            $gunModel = GunModel::create([
                'user_id' => $userId,
                'name' => $name,
            ]);

            $this->info("Created new gun model: {$gunModel->name}");
            $this->info("The observer should have dispatched a job to fetch listings for this model");
            $this->info("Check the logs for more information");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error during test: " . $e->getMessage());
            Log::error("Error during auto-dispatch test: " . $e->getMessage());
            return 1;
        }
    }
}
