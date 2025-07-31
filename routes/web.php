<?php

use App\Http\Controllers\GunModelController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard if authenticated, otherwise show login page
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Image proxy route for HTTP images
Route::get('/image-proxy', function () {
    $url = request()->query('url');
    if (!$url) {
        abort(400, 'Missing URL parameter');
    }

    try {
        $response = Http::get($url);

        if (!$response->successful()) {
            abort($response->status(), 'Failed to fetch image');
        }

        $contentType = $response->header('Content-Type');
        if (!$contentType || !str_starts_with($contentType, 'image/')) {
            $contentType = 'image/jpeg'; // Default to JPEG if content type is missing or not an image
        }

        return response($response->body())
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=86400'); // Cache for 24 hours
    } catch (\Exception $e) {
        abort(500, 'Error fetching image: ' . $e->getMessage());
    }
})->name('image.proxy');

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
