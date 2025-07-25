<?php

namespace App\Http\Controllers;

use App\Models\GunModel;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GunModel $gunModel)
    {
        // Check if the gun model belongs to the authenticated user
        if ($gunModel->user_id !== Auth::id()) {
            abort(403);
        }

        return view('listings.index', [
            'gunModel' => $gunModel,
            'listings' => $gunModel->listings()->latest()->get(),
        ]);
    }

    /**
     * Display the specified listing.
     */
    public function show(GunModel $gunModel, Listing $listing)
    {
        // Check if the gun model belongs to the authenticated user
        if ($gunModel->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if the listing belongs to the gun model
        if ($listing->gun_model_id !== $gunModel->id) {
            abort(404);
        }

        return view('listings.show', [
            'gunModel' => $gunModel,
            'listing' => $listing,
        ]);
    }
}
