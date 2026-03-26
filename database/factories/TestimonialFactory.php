<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'location' => $this->faker->city(),
            'quote' => $this->faker->paragraph(),
            'rating' => $this->faker->numberBetween(3, 5),
            'is_approved' => false,
            'is_admin_created' => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    public function adminCreated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            'is_admin_created' => true,
        ]);
    }
}
