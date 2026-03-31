<?php

namespace Tests\Feature;

use App\Livewire\Frontend\Checkout;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GiftCardCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeGiftCardProduct(): Product
    {
        return Product::factory()->create([
            'is_gift_card' => true,
            'price' => 50000,
            'allow_custom_amount' => true,
            'status' => 'active',
        ]);
    }

    private function makeRegularProduct(): Product
    {
        return Product::factory()->create([
            'is_gift_card' => false,
            'price' => 10000,
            'status' => 'active',
        ]);
    }

    private function makeCartWithItems(User $user, array $products): Cart
    {
        $cart = Cart::create(['user_id' => $user->id]);
        foreach ($products as $product) {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'custom_price' => $product->is_gift_card ? 50000 : null,
            ]);
        }

        return $cart;
    }

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'contact' => [
                'fullName' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '08012345678',
                'phoneCode' => '+234',
                'password' => null,
                'confirmPassword' => null,
            ],
            'address' => ['street' => '', 'city' => '', 'country' => ''],
            'shippingMethod' => [],
            'paymentMethod' => 'paystack',
            'promoCode' => null,
            'pointsToRedeem' => 0,
            'giftCardCode' => '',
        ], $overrides);
    }

    public function test_place_order_fails_without_shipping_address_for_physical_product(): void
    {
        $user = User::factory()->create();
        $product = $this->makeRegularProduct();
        $this->makeCartWithItems($user, [$product]);

        Livewire::actingAs($user)
            ->test(Checkout::class)
            ->call('placeOrder', $this->basePayload())
            ->assertReturned(fn ($result) => $result['success'] === false
                && str_contains($result['error'], 'shipping address'));
    }

    public function test_place_order_skips_shipping_validation_for_gift_card_only_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->makeGiftCardProduct();
        $this->makeCartWithItems($user, [$product]);

        // Gift-card-only order must NOT be rejected for missing shipping address or method
        Livewire::actingAs($user)
            ->test(Checkout::class)
            ->call('placeOrder', $this->basePayload())
            ->assertReturned(fn ($result) => ! isset($result['error'])
                || ($result['error'] !== 'Please fill in your shipping address'
                    && $result['error'] !== 'Please select a shipping method'));
    }

    public function test_place_order_requires_shipping_for_mixed_cart(): void
    {
        $user = User::factory()->create();
        $giftCard = $this->makeGiftCardProduct();
        $physical = $this->makeRegularProduct();
        $this->makeCartWithItems($user, [$giftCard, $physical]);

        Livewire::actingAs($user)
            ->test(Checkout::class)
            ->call('placeOrder', $this->basePayload())
            ->assertReturned(fn ($result) => $result['success'] === false
                && str_contains($result['error'], 'shipping address'));
    }
}
