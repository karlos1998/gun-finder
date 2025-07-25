<?php

namespace App\Livewire;

use App\Models\GunModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class GunModelList extends Component
{
    #[On('gun-model-created')]
    public function render()
    {
        return view('livewire.gun-model-list', [
            'gunModels' => Auth::user()->gunModels()->latest()->get()
        ]);
    }

    public function delete(GunModel $gunModel)
    {
        // Check if the gun model belongs to the authenticated user
        if ($gunModel->user_id !== Auth::id()) {
            return;
        }

        $gunModel->delete();
    }
}
