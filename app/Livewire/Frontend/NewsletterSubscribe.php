<?php

namespace App\Livewire\Frontend;

use App\Jobs\SendNewsletterWelcomeEmail;
use App\Models\NewsletterSubscriber;
use Illuminate\View\View;
use Livewire\Component;

class NewsletterSubscribe extends Component
{
    public string $email = '';

    public bool $subscribed = false;

    public function subscribe(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::where('email', $this->email)->first();

        if ($subscriber) {
            if (! $subscriber->isActive()) {
                $subscriber->update([
                    'unsubscribed_at' => null,
                    'subscribed_at' => now(),
                ]);
                SendNewsletterWelcomeEmail::dispatch($subscriber);
            }
        } else {
            $subscriber = NewsletterSubscriber::create([
                'email' => $this->email,
                'token' => NewsletterSubscriber::generateToken(),
                'subscribed_at' => now(),
            ]);
            SendNewsletterWelcomeEmail::dispatch($subscriber);
        }

        $this->subscribed = true;
        $this->email = '';
    }

    public function render(): View
    {
        return view('livewire.frontend.newsletter-subscribe');
    }
}
