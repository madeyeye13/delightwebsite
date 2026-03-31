<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class AdminProfile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $currentPassword = '';

    public string $newPassword = '';

    public string $confirmPassword = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function saveProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->update(['name' => $this->name, 'email' => $this->email]);

        $this->dispatch('toast', type: 'success', message: 'Profile updated successfully.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => ['required', Password::min(8)],
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $user = auth()->user();

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'The current password is incorrect.');

            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = '';
        $this->newPassword = '';
        $this->confirmPassword = '';

        $this->dispatch('toast', type: 'success', message: 'Password changed successfully.');
    }

    public function render(): View
    {
        return view('livewire.admin.admin-profile');
    }
}
