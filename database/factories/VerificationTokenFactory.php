<?php

namespace Database\Factories;

use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VerificationToken>
 */
class VerificationTokenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token' => Str::random(64),
            'type' => $this->faker->randomElement(VerificationTokenTypeEnum::cases()),
            'expires_at' => now()->addMinutes(30),
        ];
    }

    public function setType(VerificationTokenTypeEnum $verificationTokenType): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $verificationTokenType,
        ]);
    }

    public function setToken(string $token): static
    {
        return $this->state(fn(array $attributes) => [
            'token' => $token,
        ]);
    }

    public function setExpiresAt(Carbon $date): static
    {
        return $this->state(fn(array $attributes) => [
            'expires_at' => $date,
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
