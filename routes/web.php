<?php

use App\Http\Controllers\GunModelController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard if authenticated, otherwise show login page
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Gun Models
    Route::get('gun-models', function() {
        return redirect()->route('dashboard');
    })->name('');

    Route::resource('gun-models', GunModelController::class)->except(['show', 'edit', 'update', 'index']);

    // Listings
    Route::get('/gun-models/{gunModel}/listings', [ListingController::class, 'index'])->name('listings.index');
    Route::get('/gun-models/{gunModel}/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');
});
