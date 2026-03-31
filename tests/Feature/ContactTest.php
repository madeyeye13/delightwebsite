<?php

namespace Tests\Feature;

use App\Jobs\SendContactEmails;
use App\Livewire\Frontend\ContactForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_is_accessible(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Contact Us');
    }

    public function test_submitting_contact_form_creates_record_and_queues_emails(): void
    {
        Queue::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Adaeze Obi')
            ->set('email', 'adaeze@example.com')
            ->set('subject', 'Fabric inquiry')
            ->set('message', 'I would like to know more about your aso-oke collection.')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('contacts', [
            'name' => 'Adaeze Obi',
            'email' => 'adaeze@example.com',
        ]);

        Queue::assertPushed(SendContactEmails::class);
    }

    public function test_contact_form_fails_validation_without_required_fields(): void
    {
        Queue::fake();

        Livewire::test(ContactForm::class)
            ->set('name', '')
            ->set('email', '')
            ->set('message', '')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contacts', 0);
        Queue::assertNotPushed(SendContactEmails::class);
    }

    public function test_contact_form_fails_with_invalid_email(): void
    {
        Queue::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Test User')
            ->set('email', 'not-an-email')
            ->set('message', 'This is a test message with enough content.')
            ->call('submit')
            ->assertHasErrors(['email']);

        Queue::assertNotPushed(SendContactEmails::class);
    }
}
