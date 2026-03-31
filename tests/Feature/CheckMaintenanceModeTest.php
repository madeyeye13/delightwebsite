<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckMaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        AppSetting::set('maintenance_mode', '0');
    }

    public function test_frontend_accessible_when_maintenance_mode_is_off(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_frontend_returns_503_when_maintenance_mode_is_on(): void
    {
        AppSetting::set('maintenance_mode', '1');

        $response = $this->get('/');

        $response->assertStatus(503);
    }

    public function test_maintenance_page_contains_expected_content(): void
    {
        AppSetting::set('maintenance_mode', '1');

        $response = $this->get('/');

        $response->assertStatus(503);
        $response->assertSeeText("We'll be back soon", false);
    }

    public function test_admin_route_bypasses_maintenance_mode(): void
    {
        AppSetting::set('maintenance_mode', '1');

        // Unauthenticated admin-prefix requests should redirect to login (not 503)
        $response = $this->get('/admin/settings');

        $response->assertRedirect();
        $response->assertStatus(302);
    }

    public function test_admin_user_bypasses_maintenance_mode_on_frontend(): void
    {
        AppSetting::set('maintenance_mode', '1');

        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
    }

    public function test_staff_user_bypasses_maintenance_mode_on_frontend(): void
    {
        AppSetting::set('maintenance_mode', '1');

        $staff = User::factory()->create(['role' => 'staff']);
        $response = $this->actingAs($staff)->get('/');

        $response->assertStatus(200);
    }

    public function test_regular_customer_sees_maintenance_page(): void
    {
        AppSetting::set('maintenance_mode', '1');

        $customer = User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/');

        $response->assertStatus(503);
    }

    public function test_login_route_bypasses_maintenance_mode(): void
    {
        AppSetting::set('maintenance_mode', '1');

        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
