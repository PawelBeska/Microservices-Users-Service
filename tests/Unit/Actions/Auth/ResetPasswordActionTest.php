<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\ResetPasswordAction;
use App\Data\Actions\Auth\ResetPasswordData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetPasswordActionTest extends TestCase
{
    use RefreshDatabase;

    public function testResetPasswordWithValidToken(): void
    {
        $user = User::factory()->create();

        $token = VerificationToken::factory()
            ->forUser($user)
            ->setType(VerificationTokenTypeEnum::PASSWORD_RESET)
            ->create();

        $resetData = ResetPasswordData::from([
            'token' => $token->token,
            'password' => 'new-password'
        ]);

        $result = ResetPasswordAction::run($resetData);

        $this->assertTrue($result);
        $this->assertCredentials([
            'email' => $user->email,
            'password' => 'new-password',
        ]);
        $this->assertModelMissing($token);
    }

    public function testResetPasswordWithInvalidToken(): void
    {
        $user = User::factory()->create();


        $resetData = ResetPasswordData::from([
            'token' => 'invalid_token',
            'password' => 'new-password',
        ]);

        $result = ResetPasswordAction::run($resetData);

        $this->assertFalse($result);
        $this->assertCredentials([
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    public function testResetPasswordWithExpiredToken(): void
    {
        $user = User::factory()->create();

        $token = VerificationToken::factory()
            ->forUser($user)
            ->setExpiresAt(now()->subMinutes(30))
            ->setType(VerificationTokenTypeEnum::PASSWORD_RESET)
            ->create();

        $resetData = ResetPasswordData::from([
            'token' => $token->token,
            'password' => 'new-password'
        ]);

        $result = ResetPasswordAction::run($resetData);

        $this->assertFalse($result);
        $this->assertCredentials([
            'email' => $user->email,
            'password' => 'password',
        ]);
    }
}
