<?php

namespace App\Livewire\Frontend;

use App\Jobs\SendContactEmails;
use App\Models\Contact;
use Illuminate\View\View;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $contact = Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => ($this->subject ? "Subject: {$this->subject}\n\n" : '').$this->message,
        ]);

        SendContactEmails::dispatch($contact);

        $this->dispatch('toast', type: 'success', message: 'Your message has been sent! We\'ll be in touch soon.');

        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.frontend.contact-form');
    }
}
