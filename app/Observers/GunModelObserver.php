<?php

namespace App\Observers;

use App\Jobs\FetchListingsJob;
use App\Models\GunModel;
use Illuminate\Support\Facades\Log;

class GunModelObserver
{
    /**
     * Handle the GunModel "created" event.
     */
    public function created(GunModel $gunModel): void
    {
        // Dispatch a job to fetch listings for the new gun model
        FetchListingsJob::dispatch($gunModel);

        Log::info("Dispatched job to fetch listings for new gun model: {$gunModel->name}");
    }
}
