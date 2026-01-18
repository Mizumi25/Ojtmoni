<?php

namespace App\Livewire\Intern;

use Livewire\Component;

class AgencyApplication extends Component
{
    public function render()
    {
        return view('livewire.intern.agency-application')
        ->layout('layouts.auth-layout');
    }
}
