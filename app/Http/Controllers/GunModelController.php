<?php

namespace App\Http\Controllers;

use App\Models\GunModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GunModelController extends Controller
{
    /**
     * Display a listing of the gun models.
     */
    public function index()
    {
        $gunModels = Auth::user()->gunModels()->latest()->get();
        return view('gun-models.index', compact('gunModels'));
    }

    /**
     * Show the form for creating a new gun model.
     */
    public function create()
    {
        return view('gun-models.create');
    }

    /**
     * Store a newly created gun model in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        // Check if the user has reached the limit of 3 gun models
        $userGunModelsCount = Auth::user()->gunModels()->count();
        if ($userGunModelsCount >= 3) {
            return redirect()->route('gun-models.index')
                ->with('error', 'You can create a maximum of 3 gun models.');
        }

        Auth::user()->gunModels()->create($validated);

        return redirect()->route('gun-models.index')
            ->with('success', 'Gun model created successfully.');
    }

    /**
     * Remove the specified gun model from storage.
     */
    public function destroy(GunModel $gunModel)
    {
        // Check if the gun model belongs to the authenticated user
        if ($gunModel->user_id !== Auth::id()) {
            abort(403);
        }

        $gunModel->delete();

        return redirect()->route('gun-models.index')
            ->with('success', 'Gun model deleted successfully.');
    }
}
