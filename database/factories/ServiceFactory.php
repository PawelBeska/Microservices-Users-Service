<?php

namespace Database\Factories;

use App\Enums\ServiceEnum;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Service
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service' => $this->faker->randomElement(ServiceEnum::cases()),
            'host' => $this->faker->domainName(),
            'name' => $this->faker->word(),
            'port' => $this->faker->numberBetween(1, 65535),
            'is_active' => $this->faker->boolean()
        ];
    }


    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withHost(string $host): static
    {
        return $this->state(fn(array $attributes) => [
            'host' => $host,
        ]);
    }

    public function withPort(int $port): static
    {
        return $this->state(fn(array $attributes) => [
            'port' => $port,
        ]);
    }

    public function withService(ServiceEnum $service): static
    {
        return $this->state(fn(array $attributes) => [
            'service' => $service,
        ]);
    }
}
