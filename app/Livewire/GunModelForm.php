<?php

namespace App\Livewire;

use App\Models\GunModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GunModelForm extends Component
{
    public string $name = '';

    protected $rules = [
        'name' => 'required|min:3|max:255',
    ];

    public function save()
    {
        $this->validate();

        Auth::user()->gunModels()->create([
            'name' => $this->name,
        ]);

        $this->reset('name');
        $this->dispatch('gun-model-created');
    }

    public function render()
    {
        return view('livewire.gun-model-form');
    }
}
