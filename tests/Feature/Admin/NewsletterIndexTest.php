<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Newsletter\NewsletterIndex;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_newsletter_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.newsletter.index'))
            ->assertStatus(200);
    }

    public function test_guest_is_redirected_from_newsletter_page(): void
    {
        $this->get(route('admin.newsletter.index'))
            ->assertRedirect(route('login'));
    }

    public function test_subscribers_are_listed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        NewsletterSubscriber::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(NewsletterIndex::class)
            ->assertViewHas('subscribers');
    }

    public function test_admin_can_unsubscribe_a_subscriber(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subscriber = NewsletterSubscriber::factory()->create(['unsubscribed_at' => null]);

        Livewire::actingAs($admin)
            ->test(NewsletterIndex::class)
            ->call('unsubscribe', $subscriber->id);

        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_admin_can_resubscribe_an_unsubscribed_subscriber(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subscriber = NewsletterSubscriber::factory()->create(['unsubscribed_at' => now()]);

        Livewire::actingAs($admin)
            ->test(NewsletterIndex::class)
            ->call('resubscribe', $subscriber->id);

        $this->assertNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_admin_can_delete_a_subscriber(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subscriber = NewsletterSubscriber::factory()->create();

        Livewire::actingAs($admin)
            ->test(NewsletterIndex::class)
            ->call('delete', $subscriber->id);

        $this->assertDatabaseMissing('newsletter_subscribers', ['id' => $subscriber->id]);
    }

    public function test_search_filters_subscribers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        NewsletterSubscriber::factory()->create(['email' => 'adaeze@example.com']);
        NewsletterSubscriber::factory()->create(['email' => 'chidi@example.com']);

        Livewire::actingAs($admin)
            ->test(NewsletterIndex::class)
            ->set('search', 'adaeze')
            ->assertSee('adaeze@example.com')
            ->assertDontSee('chidi@example.com');
    }

    public function test_stats_returns_correct_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        NewsletterSubscriber::factory()->count(4)->create(['unsubscribed_at' => null]);
        NewsletterSubscriber::factory()->count(2)->create(['unsubscribed_at' => now()]);

        $component = Livewire::actingAs($admin)->test(NewsletterIndex::class);

        $this->assertEquals(6, $component->viewData('stats')['total']);
        $this->assertEquals(4, $component->viewData('stats')['active']);
        $this->assertEquals(2, $component->viewData('stats')['unsubscribed']);
    }
}
