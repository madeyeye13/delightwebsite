<?php

namespace Tests\Feature;

use App\Livewire\Admin\Settings\CurrencyManager;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // prevent stale getSupportedCodes() cache between tests
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────────────

    private function makeUsd(float $rate = 0.00065, float $markup = 0): Currency
    {
        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'is_default' => false,
            'is_active' => true,
            'markup' => $markup,
        ]);

        ExchangeRate::create([
            'currency_id' => $currency->id,
            'rate' => $rate,
            'fetched_at' => now(),
        ]);

        return $currency;
    }

    private function makeNgn(): Currency
    {
        return Currency::create([
            'code' => 'NGN',
            'name' => 'Nigerian Naira',
            'symbol' => '₦',
            'is_default' => true,
            'is_active' => true,
            'markup' => 0,
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // POST /currency/set
    // ────────────────────────────────────────────────────────────────────────

    public function test_guest_can_set_currency_via_route(): void
    {
        $this->makeNgn();
        $this->makeUsd();

        $response = $this->postJson('/currency/set', ['code' => 'USD']);

        $response->assertOk()->assertJson(['ok' => true, 'active' => 'USD']);
        $this->assertEquals('USD', session('user_currency'));
    }

    public function test_authenticated_user_can_set_currency_via_route(): void
    {
        $this->makeNgn();
        $this->makeUsd();
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/currency/set', ['code' => 'USD']);

        $this->assertDatabaseHas('user_currency_preferences', [
            'user_id' => $user->id,
            'currency_code' => 'USD',
        ]);
    }

    public function test_route_falls_back_to_ngn_for_unsupported_code(): void
    {
        $this->makeNgn();

        $response = $this->postJson('/currency/set', ['code' => 'XYZ']);

        $response->assertOk()->assertJson(['ok' => true, 'active' => 'NGN']);
        $this->assertEquals('NGN', session('user_currency'));
    }

    // ────────────────────────────────────────────────────────────────────────
    // CurrencyService::setUserCurrency
    // ────────────────────────────────────────────────────────────────────────

    public function test_set_user_currency_persists_to_session_for_guests(): void
    {
        $this->makeNgn();
        $this->makeUsd();

        $service = app(CurrencyService::class);
        $service->setUserCurrency('USD');

        $this->assertEquals('USD', session('user_currency'));
    }

    public function test_set_user_currency_persists_to_db_and_session_for_auth_users(): void
    {
        $this->makeNgn();
        $this->makeUsd();

        $user = User::factory()->create();
        $service = app(CurrencyService::class);

        $this->actingAs($user);
        $service->setUserCurrency('USD', $user);

        $this->assertDatabaseHas('user_currency_preferences', [
            'user_id' => $user->id,
            'currency_code' => 'USD',
        ]);
        $this->assertEquals('USD', session('user_currency'));
    }

    // ────────────────────────────────────────────────────────────────────────
    // CurrencyService::getAlpineStoreData — active must not be globally cached
    // ────────────────────────────────────────────────────────────────────────

    public function test_alpine_store_active_reflects_current_user_not_a_cached_value(): void
    {
        Cache::flush();

        $this->makeNgn();
        $this->makeUsd();

        $service = app(CurrencyService::class);

        // Guest switches to USD.
        $service->setUserCurrency('USD');
        $dataUSD = $service->getAlpineStoreData();
        $this->assertEquals('USD', $dataUSD['active']);

        // Same process — different session currency reverts to NGN.
        $service->setUserCurrency('NGN');
        $dataNGN = $service->getAlpineStoreData();
        $this->assertEquals('NGN', $dataNGN['active']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // CurrencyService::convertFromNGN — markup must be additive, not multiplicative
    // ────────────────────────────────────────────────────────────────────────

    public function test_convert_from_ngn_applies_additive_markup(): void
    {
        $this->makeNgn();
        // rate = 0.00065, markup = 4 (add $4 after conversion)
        $this->makeUsd(0.00065, 4.0);

        $service = app(CurrencyService::class);

        // 10,000 NGN × 0.00065 = $6.50, plus $4 markup = $10.50
        $result = $service->convertFromNGN(10000, 'USD');
        $this->assertEqualsWithDelta(10.50, $result, 0.01);
    }

    public function test_convert_from_ngn_with_zero_markup_is_rate_only(): void
    {
        $this->makeNgn();
        $this->makeUsd(0.00065, 0.0);

        $service = app(CurrencyService::class);

        $result = $service->convertFromNGN(10000, 'USD');
        $this->assertEqualsWithDelta(6.50, $result, 0.01);
    }

    public function test_convert_from_ngn_base_currency_returns_same_amount(): void
    {
        $this->makeNgn();
        $service = app(CurrencyService::class);

        $this->assertEqualsWithDelta(5000.0, $service->convertFromNGN(5000, 'NGN'), 0.01);
    }

    // ────────────────────────────────────────────────────────────────────────
    // ZAR seeded
    // ────────────────────────────────────────────────────────────────────────

    public function test_zar_is_a_supported_currency(): void
    {
        $this->assertContains('ZAR', CurrencyService::SUPPORTED_CURRENCIES);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Console command
    // ────────────────────────────────────────────────────────────────────────

    public function test_currency_update_rates_command_is_registered(): void
    {
        $this->artisan('currency:update-rates --help')->assertSuccessful();
    }

    // ────────────────────────────────────────────────────────────────────────
    // CurrencyService::getSupportedCodes — DB-driven
    // ────────────────────────────────────────────────────────────────────────

    public function test_get_supported_codes_returns_active_codes_from_db(): void
    {
        $this->makeNgn();
        $this->makeUsd();

        $codes = app(CurrencyService::class)->getSupportedCodes();

        $this->assertContains('NGN', $codes);
        $this->assertContains('USD', $codes);
    }

    public function test_newly_created_currency_is_accepted_by_set_user_currency(): void
    {
        $this->makeNgn();

        // Create a brand-new currency not in the original seeder (e.g. KES).
        Currency::create([
            'code' => 'KES',
            'name' => 'Kenyan Shilling',
            'symbol' => 'KSh',
            'is_active' => true,
            'is_default' => false,
            'markup' => 0,
        ]);

        Cache::flush(); // ensure getSupportedCodes() queries the DB fresh

        $service = app(CurrencyService::class);
        $service->setUserCurrency('KES');

        $this->assertEquals('KES', session('user_currency'));
    }

    // ────────────────────────────────────────────────────────────────────────
    // CurrencyManager Livewire — create & delete
    // ────────────────────────────────────────────────────────────────────────

    public function test_admin_can_delete_non_default_currency(): void
    {
        $ngn = $this->makeNgn();
        $usd = $this->makeUsd();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(CurrencyManager::class)
            ->call('deleteCurrency', $usd->id);

        $this->assertDatabaseMissing('currencies', ['code' => 'USD']);
    }

    public function test_admin_cannot_delete_default_currency(): void
    {
        $ngn = $this->makeNgn();
        $ngn->update(['is_default' => true]);

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(CurrencyManager::class)
            ->call('deleteCurrency', $ngn->id);

        $this->assertDatabaseHas('currencies', ['code' => 'NGN']);
    }
}
