<?php

namespace App\Livewire\Intern;

use Livewire\Component;

class OrientationResources extends Component
{
    public function render()
    {
        return view('livewire.intern.orientation-resources')
        ->layout('layouts.auth-layout');
    }
}
