<?php

namespace App\Console\Commands;

use App\Models\GunModel;
use Illuminate\Console\Command;

class DeleteGunModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gun-models:delete {--all : Delete all gun models} {--id= : Delete a specific gun model by ID} {--user= : Delete all gun models for a specific user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete gun models with their details from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            // Delete all gun models
            $count = GunModel::count();

            if ($count === 0) {
                $this->info('No gun models found in the database.');
                return 0;
            }

            if ($this->confirm("Are you sure you want to delete all {$count} gun models? This action cannot be undone.")) {
                GunModel::query()->delete();
                $this->info("All gun models have been deleted.");
            } else {
                $this->info('Operation cancelled.');
            }
        } elseif ($this->option('id')) {
            // Delete a specific gun model by ID
            $id = $this->option('id');
            $gunModel = GunModel::find($id);

            if (!$gunModel) {
                $this->error("Gun model with ID {$id} not found.");
                return 1;
            }

            if ($this->confirm("Are you sure you want to delete the gun model '{$gunModel->name}'? This action cannot be undone.")) {
                $gunModel->delete();
                $this->info("Gun model '{$gunModel->name}' has been deleted.");
            } else {
                $this->info('Operation cancelled.');
            }
        } elseif ($this->option('user')) {
            // Delete all gun models for a specific user
            $userId = $this->option('user');
            $count = GunModel::where('user_id', $userId)->count();

            if ($count === 0) {
                $this->info("No gun models found for user with ID {$userId}.");
                return 0;
            }

            if ($this->confirm("Are you sure you want to delete all {$count} gun models for user with ID {$userId}? This action cannot be undone.")) {
                GunModel::where('user_id', $userId)->delete();
                $this->info("All gun models for user with ID {$userId} have been deleted.");
            } else {
                $this->info('Operation cancelled.');
            }
        } else {
            // No option provided, show help
            $this->info('Please provide one of the following options:');
            $this->info('  --all : Delete all gun models');
            $this->info('  --id=ID : Delete a specific gun model by ID');
            $this->info('  --user=USER_ID : Delete all gun models for a specific user');
            $this->info('');
            $this->info('Example: php artisan gun-models:delete --all');
        }

        return 0;
    }
}
