<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Contacts\ContactIndex;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_contacts_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.contacts.index'))
            ->assertStatus(200);
    }

    public function test_guest_is_redirected_from_contacts_page(): void
    {
        $this->get(route('admin.contacts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_contacts_are_listed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Contact::factory()->count(5)->create();

        Livewire::actingAs($admin)
            ->test(ContactIndex::class)
            ->assertViewHas('contacts');
    }

    public function test_admin_can_mark_contact_as_read(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $contact = Contact::factory()->create(['read_at' => null]);

        Livewire::actingAs($admin)
            ->test(ContactIndex::class)
            ->call('markAsRead', $contact->id);

        $this->assertNotNull($contact->fresh()->read_at);
    }

    public function test_admin_can_mark_contact_as_unread(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $contact = Contact::factory()->create(['read_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ContactIndex::class)
            ->call('markAsUnread', $contact->id);

        $this->assertNull($contact->fresh()->read_at);
    }

    public function test_admin_can_delete_a_contact(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $contact = Contact::factory()->create();

        Livewire::actingAs($admin)
            ->test(ContactIndex::class)
            ->call('delete', $contact->id);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_viewing_a_message_marks_it_as_read(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $contact = Contact::factory()->create(['read_at' => null]);

        Livewire::actingAs($admin)
            ->test(ContactIndex::class)
            ->call('viewMessage', $contact->id);

        $this->assertNotNull($contact->fresh()->read_at);
    }

    public function test_search_filters_contacts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Contact::factory()->create(['name' => 'Adaeze Okonkwo']);
        Contact::factory()->create(['name' => 'Chidi Nwosu']);

        Livewire::actingAs($admin)
            ->test(ContactIndex::class)
            ->set('search', 'Adaeze')
            ->assertSee('Adaeze Okonkwo')
            ->assertDontSee('Chidi Nwosu');
    }

    public function test_unread_filter_shows_only_unread_contacts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Contact::factory()->create(['name' => 'Unread Person', 'read_at' => null]);
        Contact::factory()->create(['name' => 'Read Person', 'read_at' => now()]);

        Livewire::actingAs($admin)
            ->test(ContactIndex::class)
            ->set('statusFilter', 'unread')
            ->assertSee('Unread Person')
            ->assertDontSee('Read Person');
    }
}
