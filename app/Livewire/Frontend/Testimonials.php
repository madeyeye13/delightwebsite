<?php

namespace App\Livewire\Frontend;

use App\Jobs\SendTestimonialNotificationEmail;
use App\Models\Testimonial;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Testimonials extends Component
{
    public bool $submitted = false;

    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('nullable|string|max:100')]
    public string $location = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $quote = '';

    #[Validate('nullable|integer|min:1|max:5')]
    public ?int $rating = null;

    /** @var Collection<int, Testimonial> */
    public Collection $testimonials;

    public function mount(): void
    {
        $this->loadTestimonials();
    }

    private function loadTestimonials(): void
    {
        $this->testimonials = Testimonial::approved()
            ->latest()
            ->get();
    }

    public function submit(): void
    {
        $this->validate();

        $testimonial = Testimonial::create([
            'name' => $this->name,
            'location' => $this->location ?: null,
            'quote' => $this->quote,
            'rating' => $this->rating,
            'is_approved' => false,
            'is_admin_created' => false,
        ]);

        SendTestimonialNotificationEmail::dispatch($testimonial);

        $this->reset(['name', 'location', 'quote', 'rating']);
        $this->submitted = true;

        $this->dispatch('toast', type: 'success', message: 'Thank you! Your review has been submitted and will go live after approval.');
    }

    public function render(): View
    {
        return view('livewire.frontend.testimonials');
    }
}
