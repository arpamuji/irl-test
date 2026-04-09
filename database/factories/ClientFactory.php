<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'src_code' => $this->faker->regexify('[A-Z]{3}-[0-9]{5}'),
            'name' => $this->faker->company(),
            'short_name' => $this->faker->optional()->word(),
        ];
    }

    public function withoutSrcCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'src_code' => null,
        ]);
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    public function withSrcCode(string $srcCode): static
    {
        return $this->state(fn (array $attributes) => [
            'src_code' => $srcCode,
        ]);
    }
}
