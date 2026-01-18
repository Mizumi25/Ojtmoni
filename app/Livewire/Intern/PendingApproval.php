<?php

namespace App\Livewire\Intern;

use Livewire\Component;

class PendingApproval extends Component
{
    public function render()
    {
        return view('livewire.intern.pending-approval')
        ->layout('layouts.auth-layout');
    }
}
