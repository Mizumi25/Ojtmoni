<?php

namespace App\Livewire\Intern;

use Livewire\Component;

class AgencyReports extends Component
{
    public function render()
    {
        return view('livewire.intern.agency-reports')
        ->layout('layouts.auth-layout');
    }
}
