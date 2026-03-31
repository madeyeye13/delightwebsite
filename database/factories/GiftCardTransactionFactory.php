<?php

namespace Database\Factories;

use App\Models\GiftCardTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GiftCardTransaction>
 */
class GiftCardTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $before = fake()->numberBetween(5000, 50000);
        $used = fake()->numberBetween(1000, $before);

        return [
            'amount_used' => $used,
            'balance_before' => $before,
            'balance_after' => $before - $used,
            'is_pos_redemption' => false,
            'notes' => null,
        ];
    }
}
