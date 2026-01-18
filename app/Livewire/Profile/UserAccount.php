<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;



class UserAccount extends Component
{
    use WithFileUploads;

    public $user;
    public $name, $email, $phone_number, $ojt_info;
    public $editing = false;
    public $profile_picture;
    public $new_profile_picture;
    public $current_password, $new_password, $confirm_password;

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone_number = $this->user->phone_number;
        $this->ojt_info = optional($this->user->agency)->name;
        $this->profile_picture = $this->user->profile_picture;
    }

    public function edit()
    {
        $this->editing = true;
    }

    public function cancel()
    {
        $this->editing = false;
        $this->mount(); // reset values
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $this->user->update([
            'name' => $this->name,
            'phone_number' => $this->phone_number,
        ]);

        $this->editing = false;
    }

    public function savePicture()
    {
        if ($this->new_profile_picture) {
            $path = $this->new_profile_picture->store('profile_pictures', 'public');
            $this->user->update(['profile_picture' => $path]);
            $this->profile_picture = $path;
            $this->user = $this->user->fresh(); // Refresh user data
            $this->new_profile_picture = null;
        }
    }

    
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'New password and confirmation do not match.',
        ]);
    
        if (!Hash::check($this->current_password, $this->user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }
    
        $this->user->update([
            'password' => bcrypt($this->new_password),
        ]);
    
        // Clear fields
        $this->current_password = $this->new_password = $this->confirm_password = null;
    
        session()->flash('message', 'Password updated successfully.');
    }


    public function render()
    {
        return view('livewire.profile.user-account')
            ->layout('layouts.auth-layout');
    }
}
