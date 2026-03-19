<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('My Profile')]
class Profile extends Component
{
    public string $name          = '';
    public string $email         = '';
    public string $currentPassword = '';
    public string $newPassword   = '';
    public string $confirmPassword = '';

    public bool $profileSaved  = false;
    public bool $passwordSaved = false;

    public function mount(): void
    {
        $user        = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
    }

    public function saveProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update(['name' => $this->name, 'email' => $this->email]);

        $this->profileSaved = true;
        $this->dispatch('profile-saved');
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword'     => ['required', Password::min(8)],
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $user = auth()->user();

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'The current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = '';
        $this->newPassword     = '';
        $this->confirmPassword = '';
        $this->passwordSaved   = true;
        $this->dispatch('password-changed');
    }

    public function render()
    {
        return view('livewire.dashboard.profile');
    }
}