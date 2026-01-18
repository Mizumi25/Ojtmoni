<?php

namespace App\Livewire\Nav;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public $showDropdown = false;
    public $showModal = false;

    public function render()
    {
        return view('livewire.nav.header', [
            'user' => Auth::user()
        ]);
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/login');
    }
}