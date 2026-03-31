<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AdminSettings;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.settings'))
            ->assertStatus(200);
    }

    public function test_guest_is_redirected_from_settings_page(): void
    {
        $this->get(route('admin.settings'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_save_general_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(AdminSettings::class)
            ->set('store_name', 'Delight Store')
            ->set('store_email', 'store@example.com')
            ->call('saveGeneral')
            ->assertHasNoErrors();

        $this->assertEquals('Delight Store', AppSetting::get('store_name'));
        $this->assertEquals('store@example.com', AppSetting::get('store_email'));
    }

    public function test_save_general_validates_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(AdminSettings::class)
            ->set('store_name', '')
            ->set('store_email', 'not-an-email')
            ->call('saveGeneral')
            ->assertHasErrors(['store_name', 'store_email']);
    }

    public function test_admin_can_save_email_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(AdminSettings::class)
            ->set('mail_from_name', 'Delight Mailer')
            ->set('mail_from_address', 'no-reply@example.com')
            ->set('admin_notification_email', 'admin@example.com')
            ->call('saveEmail')
            ->assertHasNoErrors();

        $this->assertEquals('Delight Mailer', AppSetting::get('mail_from_name'));
    }

    public function test_admin_can_save_seo_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(AdminSettings::class)
            ->set('seo_title', 'Delight | Best Fabrics')
            ->set('seo_description', 'Shop the finest fabrics online.')
            ->call('saveSeo')
            ->assertHasNoErrors();

        $this->assertEquals('Delight | Best Fabrics', AppSetting::get('seo_title'));
    }

    public function test_admin_can_save_security_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(AdminSettings::class)
            ->set('session_lifetime', 60)
            ->call('saveSecurity')
            ->assertHasNoErrors();

        $this->assertEquals('60', AppSetting::get('session_lifetime'));
    }

    public function test_session_lifetime_must_be_at_least_15_minutes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(AdminSettings::class)
            ->set('session_lifetime', 5)
            ->call('saveSecurity')
            ->assertHasErrors(['session_lifetime']);
    }

    public function test_admin_can_toggle_notification_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(AdminSettings::class)
            ->set('notify_new_order', false)
            ->set('notify_new_contact', true)
            ->set('mail_from_name', 'Test')
            ->set('mail_from_address', 'test@example.com')
            ->set('admin_notification_email', 'admin@example.com')
            ->call('saveEmail')
            ->assertHasNoErrors();

        $this->assertEquals('0', AppSetting::get('notify_new_order'));
        $this->assertEquals('1', AppSetting::get('notify_new_contact'));
    }
}
