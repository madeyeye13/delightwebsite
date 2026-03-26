<?php

namespace App\Livewire\Admin\Testimonials;

use App\Models\Testimonial;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TestimonialsIndex extends Component
{
    public string $filterStatus = 'all';

    #[Validate('required|string|max:100')]
    public string $createName = '';

    #[Validate('nullable|string|max:100')]
    public string $createLocation = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $createQuote = '';

    #[Validate('nullable|integer|min:1|max:5')]
    public ?int $createRating = null;

    public function approve(int $id): void
    {
        Testimonial::findOrFail($id)->update(['is_approved' => true]);
        $this->dispatch('toast', type: 'success', message: 'Testimonial approved and is now live.');
    }

    public function unapprove(int $id): void
    {
        Testimonial::findOrFail($id)->update(['is_approved' => false]);
        $this->dispatch('toast', type: 'info', message: 'Testimonial unpublished.');
    }

    public function delete(int $id): void
    {
        Testimonial::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Testimonial deleted.');
    }

    public function create(): void
    {
        $this->validateOnly('createName');
        $this->validateOnly('createLocation');
        $this->validateOnly('createQuote');
        $this->validateOnly('createRating');

        Testimonial::create([
            'name' => $this->createName,
            'location' => $this->createLocation ?: null,
            'quote' => $this->createQuote,
            'rating' => $this->createRating,
            'is_approved' => true,
            'is_admin_created' => true,
        ]);

        $this->resetCreateForm();
        $this->dispatch('close-create-modal');
        $this->dispatch('toast', type: 'success', message: 'Testimonial created and published.');
    }

    private function resetCreateForm(): void
    {
        $this->createName = '';
        $this->createLocation = '';
        $this->createQuote = '';
        $this->createRating = null;
    }

    public function render(): View
    {
        $query = Testimonial::query()->latest();

        if ($this->filterStatus === 'pending') {
            $query->where('is_approved', false);
        } elseif ($this->filterStatus === 'approved') {
            $query->where('is_approved', true);
        }

        $testimonials = $query->get();

        $stats = [
            'total' => Testimonial::count(),
            'approved' => Testimonial::where('is_approved', true)->count(),
            'pending' => Testimonial::where('is_approved', false)->count(),
        ];

        return view('livewire.admin.testimonials.testimonials-index', [
            'testimonials' => $testimonials,
            'stats' => $stats,
        ]);
    }
}
