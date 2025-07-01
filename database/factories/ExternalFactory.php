<?php

namespace Database\Factories;

use App\Enums\ServiceEnum;
use App\Models\External;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends External
 */
class ExternalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'external_id' => $this->faker->numberBetween(1000, 99999),
        ];
    }

    public function withService(ServiceEnum $service): static
    {
        return $this->state(fn(array $attributes) => [
            'service_id' => Service::query()->firstWhere('service', $service)?->id ?? Service::factory()->withService($service)->create()->id,
        ]);
    }
}
