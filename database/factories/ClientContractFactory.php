<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class ClientContractFactory extends Factory
{
    protected $model = ClientContract::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->optional(0.8)->dateTimeBetween('-1 year', '+1 year');
        $endDate = $startDate ? $this->faker->dateTimeBetween($startDate, '+2 years') : null;

        return [
            'client_id' => Client::factory(),
            'src_code' => $this->faker->optional()->regexify('CTRC-[0-9]{3}-[A-Z]{3}'),
            'name' => $this->faker->words(3, true),
            'short_name' => $this->faker->optional()->word(),
            'start_date' => $startDate?->format('Y-m-d'),
            'end_date' => $endDate?->format('Y-m-d'),
            'monthly_value' => $this->faker->optional()->randomFloat(4, 1000, 10000),
        ];
    }

    // Active contract within date range
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'start_date' => now()->subMonths(2)->format('Y-m-d'),
            'end_date' => now()->addMonths(12)->format('Y-m-d'),
        ]);
    }

    // Expired contract
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'start_date' => now()->subMonths(12)->format('Y-m-d'),
            'end_date' => now()->subMonths(2)->format('Y-m-d'),
        ]);
    }

    // Future contract, not started yet
    public function future(): static
    {
        return $this->state(fn(array $attributes) => [
            'start_date' => now()->addMonths(2)->format('Y-m-d'),
            'end_date' => now()->addMonths(14)->format('Y-m-d'),
        ]);
    }

    // Contract with no end date (always active)
    public function unbounded(): static
    {
        return $this->state(fn(array $attributes) => [
            'start_date' => null,
            'end_date' => null,
        ]);
    }

    // Zero monthly value contract
    public function zeroValue(): static
    {
        return $this->state(fn() => [
            'monthly_value' => 0,
        ]);
    }

    // Deleted contract (soft deleted)
    public function deleted(): static
    {
        return $this->state(fn(array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    // Associate contract with specific client
    public function forClient(Client|int $client): static
    {
        return $this->state(fn(array $attributes) => [
            'client_id' => $client instanceof Client ? $client->id : $client,
        ]);
    }
}
