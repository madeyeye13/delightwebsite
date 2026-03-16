<?php

namespace App\Livewire\Frontend;

use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProductReviews extends Component
{
    public int $productId = 0;

    public bool $showForm = false;

    public bool $submitted = false;

    public bool $userHasReviewed = false;

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 0;

    #[Validate('nullable|string|max:100')]
    public string $title = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $body = '';

    /** @var Collection<int, Review> */
    public Collection $reviews;

    public float $avgRating = 0;

    public int $reviewCount = 0;

    /** @var array<int, int> */
    public array $ratingBreakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

    public function mount(int $productId): void
    {
        $this->productId = $productId;
        $this->loadReviews();
    }

    private function loadReviews(): void
    {
        $this->reviews = Review::with('user')
            ->where('product_id', $this->productId)
            ->approved()
            ->latest()
            ->get();

        $this->reviewCount = $this->reviews->count();
        $this->avgRating = $this->reviewCount ? round($this->reviews->avg('rating'), 1) : 0;

        $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($this->reviews as $review) {
            $breakdown[(int) $review->rating] = ($breakdown[(int) $review->rating] ?? 0) + 1;
        }

        foreach ($breakdown as $star => $count) {
            $this->ratingBreakdown[$star] = $this->reviewCount > 0
                ? (int) round(($count / $this->reviewCount) * 100)
                : 0;
        }

        if (Auth::check()) {
            $this->userHasReviewed = $this->reviews->contains('user_id', Auth::id());
        }
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function toggleForm(): void
    {
        if (! Auth::check()) {
            $this->dispatch('open-auth-modal');

            return;
        }

        $this->showForm = ! $this->showForm;
    }

    public function submitReview(): void
    {
        if (! Auth::check()) {
            $this->dispatch('open-auth-modal');

            return;
        }

        $this->validate();

        Review::updateOrCreate(
            ['product_id' => $this->productId, 'user_id' => Auth::id()],
            [
                'rating' => $this->rating,
                'title' => $this->title ?: null,
                'body' => $this->body,
                'is_approved' => false,
            ]
        );

        $this->reset('rating', 'title', 'body', 'showForm');
        $this->submitted = true;
        $this->loadReviews();
    }

    public function render(): View
    {
        return view('livewire.frontend.product-reviews');
    }
}
