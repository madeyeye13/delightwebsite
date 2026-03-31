<?php

namespace Tests\Feature;

use App\Jobs\SendNewsletterWelcomeEmail;
use App\Livewire\Frontend\NewsletterSubscribe;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterSubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_subscriber_is_created_and_welcome_email_queued(): void
    {
        Queue::fake();

        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'temi@example.com')
            ->call('subscribe')
            ->assertSet('subscribed', true);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'temi@example.com',
        ]);

        $subscriber = NewsletterSubscriber::where('email', 'temi@example.com')->first();
        $this->assertNotNull($subscriber->subscribed_at);
        $this->assertNull($subscriber->unsubscribed_at);
        $this->assertNotEmpty($subscriber->token);

        Queue::assertPushed(SendNewsletterWelcomeEmail::class);
    }

    public function test_existing_active_subscriber_does_not_get_duplicate_record(): void
    {
        Queue::fake();

        NewsletterSubscriber::factory()->create([
            'email' => 'temi@example.com',
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'temi@example.com')
            ->call('subscribe')
            ->assertSet('subscribed', true);

        $this->assertDatabaseCount('newsletter_subscribers', 1);
        Queue::assertNotPushed(SendNewsletterWelcomeEmail::class);
    }

    public function test_unsubscribed_user_can_resubscribe(): void
    {
        Queue::fake();

        $subscriber = NewsletterSubscriber::factory()->create([
            'email' => 'temi@example.com',
            'subscribed_at' => now()->subMonth(),
            'unsubscribed_at' => now()->subDay(),
        ]);

        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'temi@example.com')
            ->call('subscribe')
            ->assertSet('subscribed', true);

        $this->assertDatabaseCount('newsletter_subscribers', 1);
        $subscriber->refresh();
        $this->assertNull($subscriber->unsubscribed_at);

        Queue::assertPushed(SendNewsletterWelcomeEmail::class);
    }

    public function test_subscription_fails_with_invalid_email(): void
    {
        Queue::fake();

        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'not-valid')
            ->call('subscribe')
            ->assertHasErrors(['email']);

        $this->assertDatabaseCount('newsletter_subscribers', 0);
        Queue::assertNotPushed(SendNewsletterWelcomeEmail::class);
    }

    public function test_unsubscribe_route_marks_subscriber_as_unsubscribed(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        $response = $this->get(route('newsletter.unsubscribe', $subscriber->token));

        $response->assertStatus(200);
        $response->assertSee('unsubscribed');

        $subscriber->refresh();
        $this->assertNotNull($subscriber->unsubscribed_at);
    }

    public function test_unsubscribe_route_returns_404_for_invalid_token(): void
    {
        $response = $this->get(route('newsletter.unsubscribe', 'invalid-token-that-does-not-exist'));

        $response->assertStatus(404);
    }
}
