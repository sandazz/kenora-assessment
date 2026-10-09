<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory(),
            'attendee_name' => fake()->name(),
            'attendee_email' => strtolower(fake()->unique()->safeEmail()),
            'status' => RegistrationStatus::Active,
            'registered_by' => User::factory()->staff(),
            'registered_at' => now(),
            'cancelled_by' => null,
            'cancelled_at' => null,
            'cancel_reason' => null,
            'active_key' => 1,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => RegistrationStatus::Cancelled,
                'cancelled_by' => User::factory()->staff(),
                'cancelled_at' => now(),
                'cancel_reason' => fake()->optional()->sentence(),
                'active_key' => null,
            ];
        });
    }
}
