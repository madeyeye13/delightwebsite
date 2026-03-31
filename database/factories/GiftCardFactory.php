<?php

namespace Database\Factories;

use App\Models\GiftCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GiftCard>
 */
class GiftCardFactory extends Factory
{
    public function definition(): array
    {
        $balance = fake()->randomElement([5000, 10000, 25000, 50000]);

        return [
            'code' => 'DLT-'.strtoupper(fake()->bothify('????-????-????')),
            'status' => 'active',
            'initial_balance' => $balance,
            'current_balance' => $balance,
            'recipient_email' => fake()->optional()->safeEmail(),
            'recipient_name' => fake()->optional()->name(),
            'is_pos_issued' => false,
            'expires_at' => null,
        ];
    }

    public function redeemed(): static
    {
        return $this->state(fn () => [
            'status' => 'redeemed',
            'current_balance' => 0,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'expires_at' => now()->subDay(),
        ]);
    }
}
