<?php

namespace Tests\Feature;

use App\Jobs\SendTestimonialNotificationEmail;
use App\Livewire\Admin\Testimonials\TestimonialsIndex;
use App\Livewire\Frontend\Testimonials;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class TestimonialsTest extends TestCase
{
    use RefreshDatabase;

    // ── Frontend ─────────────────────────────────────────────────────────────

    public function test_approved_testimonials_display_on_frontend(): void
    {
        Testimonial::factory()->approved()->count(3)->create();
        Testimonial::factory()->create(); // pending, should not show

        $component = Livewire::test(Testimonials::class);

        $component->assertSet('testimonials', fn ($t) => $t->count() === 3);
    }

    public function test_customer_can_submit_testimonial(): void
    {
        Queue::fake();

        $component = Livewire::test(Testimonials::class);

        $component
            ->set('name', 'Adaeze Okonkwo')
            ->set('location', 'Abuja')
            ->set('quote', 'Absolutely love the quality of this fabric store!')
            ->set('rating', 5)
            ->call('submit');

        $component->assertSet('submitted', true);
        $component->assertDispatched('toast');

        $this->assertDatabaseHas('testimonials', [
            'name' => 'Adaeze Okonkwo',
            'location' => 'Abuja',
            'is_approved' => false,
        ]);

        Queue::assertPushed(SendTestimonialNotificationEmail::class);
    }

    public function test_submission_fails_without_required_fields(): void
    {
        Queue::fake();

        $component = Livewire::test(Testimonials::class);

        $component->set('name', '')->set('quote', '')->call('submit');

        $component->assertHasErrors(['name', 'quote']);

        Queue::assertNotPushed(SendTestimonialNotificationEmail::class);
    }

    public function test_submission_fails_with_quote_too_short(): void
    {
        $component = Livewire::test(Testimonials::class);

        $component->set('name', 'Ada')->set('quote', 'short')->call('submit');

        $component->assertHasErrors(['quote']);
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    public function test_admin_can_approve_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $testimonial = Testimonial::factory()->create(['is_approved' => false]);

        Livewire::actingAs($admin)
            ->test(TestimonialsIndex::class)
            ->call('approve', $testimonial->id);

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'is_approved' => true,
        ]);
    }

    public function test_admin_can_unpublish_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $testimonial = Testimonial::factory()->approved()->create();

        Livewire::actingAs($admin)
            ->test(TestimonialsIndex::class)
            ->call('unapprove', $testimonial->id);

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'is_approved' => false,
        ]);
    }

    public function test_admin_can_delete_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $testimonial = Testimonial::factory()->create();

        Livewire::actingAs($admin)
            ->test(TestimonialsIndex::class)
            ->call('delete', $testimonial->id);

        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }

    public function test_admin_can_create_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(TestimonialsIndex::class)
            ->set('createName', 'Chisom Nwosu')
            ->set('createLocation', 'Lagos')
            ->set('createQuote', 'Best fabric store I have found in Nigeria. The quality is consistent.')
            ->set('createRating', 5)
            ->call('create');

        $this->assertDatabaseHas('testimonials', [
            'name' => 'Chisom Nwosu',
            'is_approved' => true,
            'is_admin_created' => true,
        ]);
    }

    public function test_admin_create_dispatches_toast(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $component = Livewire::actingAs($admin)->test(TestimonialsIndex::class);

        $component
            ->set('createName', 'Test User')
            ->set('createLocation', 'Lagos')
            ->set('createQuote', 'Really great products and fast delivery service.')
            ->call('create');

        $component->assertDispatched('toast');
    }
}
