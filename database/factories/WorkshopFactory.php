<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\WorkshopStatus;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    protected $model = Workshop::class;

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+30 days');
        $endsAt = (clone $startsAt)->modify('+2 hours');

        return [
            'code' => strtoupper(fake()->lexify('???').'-'.fake()->numerify('###')),
            'title' => fake()->sentence(3),
            'instructor' => fake()->name(),
            'location' => fake()->randomElement(['Downtown Centre', 'Westside Branch', 'Northgate Hub']),
            'description' => fake()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'capacity' => fake()->numberBetween(5, 20),
            'status' => WorkshopStatus::Scheduled,
            'created_by' => User::factory()->manager(),
            'updated_by' => null,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkshopStatus::Scheduled,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkshopStatus::Cancelled,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkshopStatus::Completed,
            'starts_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'ends_at' => fake()->dateTimeBetween('-29 days', '-1 day'),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'ends_at' => fake()->dateTimeBetween('-29 days', '-1 day'),
        ]);
    }
}
