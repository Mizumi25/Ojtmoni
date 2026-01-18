<?php

namespace App\Livewire\Intern;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\UserMapExposureChanged;

use Livewire\Component;

class Dashboard4 extends Component
{
    public bool $mapExposed;

    public function mount()
    {
        $this->mapExposed = Auth::user()->map_exposed;
    }

    public function toggleMapExposed(bool $value)
    {
        $user = Auth::user();
        $user->map_exposed = $value;
        $user->save();
        
        broadcast(new UserMapExposureChanged($user->id, $user->map_exposed));
        
        $this->mapExposed = $value; // Update the component property
    }

    public function render()
    {
        return view('livewire.intern.dashboard4')->layout('layouts.auth-layout');
    }
}
