<?php

namespace App\Providers;

use App\Models\GunModel;
use App\Observers\GunModelObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        GunModel::observe(GunModelObserver::class);
    }
}
