<?php

use App\Jobs\FetchListingsBatchJob;
use App\Jobs\FetchListingsJob;
use App\Models\GunModel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fetch:listings', function () {
    $this->info('Fetching listings from Netgun.pl...');

    // Dispatch a batch job to fetch listings for all gun models
    FetchListingsBatchJob::dispatch();

    $this->info('Batch job dispatched. Listings will be processed in the background.');
})->purpose('Fetch listings from Netgun.pl for all gun models');

\Illuminate\Support\Facades\Schedule::job(new FetchListingsBatchJob)->everythirtyMinutes();
