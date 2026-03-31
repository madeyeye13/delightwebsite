<?php

namespace Tests\Feature;

use App\Models\GiftCard;
use App\Models\Product;
use App\Models\User;
use App\Services\GiftCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardTest extends TestCase
{
    use RefreshDatabase;

    private GiftCardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(GiftCardService::class);
    }

    public function test_generate_code_produces_correct_format(): void
    {
        $code = $this->service->generateCode();

        $this->assertMatchesRegularExpression('/^DLT-[A-Z2-9]{4}-[A-Z2-9]{4}-[A-Z2-9]{4}$/', $code);
    }

    public function test_generate_code_is_unique(): void
    {
        $codes = array_map(fn () => $this->service->generateCode(), range(1, 20));
        $unique = array_unique($codes);

        $this->assertCount(20, $unique, 'All generated codes should be unique');
    }

    public function test_validate_returns_invalid_for_nonexistent_code(): void
    {
        $result = $this->service->validate('DLT-XXXX-XXXX-XXXX');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('not found', strtolower($result['message']));
    }

    public function test_validate_returns_valid_for_active_card_with_balance(): void
    {
        $card = GiftCard::factory()->create([
            'code' => 'DLT-TEST-ABCD-1234',
            'status' => 'active',
            'current_balance' => 50000,
        ]);

        $result = $this->service->validate($card->code);

        $this->assertTrue($result['valid']);
        $this->assertEquals(50000, $result['balance']);
        $this->assertSame($card->id, $result['card']->id);
    }

    public function test_validate_returns_invalid_for_redeemed_card(): void
    {
        $card = GiftCard::factory()->redeemed()->create();

        $result = $this->service->validate($card->code);

        $this->assertFalse($result['valid']);
    }

    public function test_validate_returns_invalid_for_expired_card(): void
    {
        $card = GiftCard::factory()->expired()->create();

        $result = $this->service->validate($card->code);

        $this->assertFalse($result['valid']);
    }

    public function test_validate_returns_invalid_for_zero_balance_card(): void
    {
        $card = GiftCard::factory()->create([
            'status' => 'active',
            'current_balance' => 0,
        ]);

        $result = $this->service->validate($card->code);

        $this->assertFalse($result['valid']);
    }

    public function test_redeem_for_pos_deducts_correct_amount(): void
    {
        $admin = User::factory()->create();
        $card = GiftCard::factory()->create([
            'status' => 'active',
            'current_balance' => 50000,
        ]);

        $result = $this->service->redeemForPos(
            card: $card,
            orderAmount: 30000,
            adminId: $admin->id,
            notes: 'In-store test',
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(30000, $result['applied']);
        $this->assertEquals(0, $result['remaining']);
        $this->assertEquals(20000, $result['card_balance_after']);

        $card->refresh();
        $this->assertEquals(20000, $card->current_balance);
    }

    public function test_redeem_for_pos_caps_at_card_balance(): void
    {
        $admin = User::factory()->create();
        $card = GiftCard::factory()->create([
            'status' => 'active',
            'current_balance' => 10000,
        ]);

        $result = $this->service->redeemForPos(
            card: $card,
            orderAmount: 50000,
            adminId: $admin->id,
            notes: '',
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(10000, $result['applied']);
        $this->assertEquals(40000, $result['remaining']);
        $this->assertEquals(0, $result['card_balance_after']);

        $card->refresh();
        $this->assertEquals(0, $card->current_balance);
        $this->assertEquals('redeemed', $card->status);
    }

    public function test_redeem_for_pos_creates_transaction_record(): void
    {
        $admin = User::factory()->create();
        $card = GiftCard::factory()->create([
            'status' => 'active',
            'current_balance' => 20000,
        ]);

        $this->service->redeemForPos(
            card: $card,
            orderAmount: 15000,
            adminId: $admin->id,
            notes: 'Receipt #99',
        );

        $transaction = $card->transactions()->latest()->first();

        $this->assertNotNull($transaction);
        $this->assertEquals(15000, $transaction->amount_used);
        $this->assertEquals(20000, $transaction->balance_before);
        $this->assertEquals(5000, $transaction->balance_after);
        $this->assertTrue((bool) $transaction->is_pos_redemption);
        $this->assertEquals($admin->id, $transaction->redeemed_by_admin_id);
        $this->assertStringContainsString('Receipt #99', $transaction->notes);
    }

    public function test_gift_card_product_flag_included_in_storefront_array(): void
    {
        $product = Product::factory()->create([
            'is_gift_card' => true,
            'allow_custom_amount' => true,
        ]);

        $data = $product->toStorefrontArray();

        $this->assertTrue($data['isGiftCard']);
        $this->assertTrue($data['allowCustomAmount']);
    }

    public function test_get_notification_email_prefers_recipient_email(): void
    {
        $purchaser = User::factory()->create(['email' => 'buyer@example.com']);
        $card = GiftCard::factory()->create([
            'purchased_by_user_id' => $purchaser->id,
            'recipient_email' => 'recipient@example.com',
        ]);

        $this->assertEquals('recipient@example.com', $card->getNotificationEmail());
    }

    public function test_get_notification_email_falls_back_to_purchaser_email(): void
    {
        $purchaser = User::factory()->create(['email' => 'buyer@example.com']);
        $card = GiftCard::factory()->create([
            'purchased_by_user_id' => $purchaser->id,
            'recipient_email' => null,
        ]);

        $this->assertEquals('buyer@example.com', $card->getNotificationEmail());
    }

    public function test_admin_gift_cards_route_requires_auth(): void
    {
        $response = $this->get('/admin/gift-cards');

        $response->assertRedirect();
    }

    public function test_admin_gift_cards_route_accessible_by_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/gift-cards');

        $response->assertOk();
    }
}
