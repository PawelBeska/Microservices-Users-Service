<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\VerifyEmailAction;
use App\Data\Actions\Auth\VerifyEmailData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyEmailActionTest extends TestCase
{
    use RefreshDatabase;

    public function testVerifyEmailWithValidToken(): void
    {
        $user = User::factory()->unverified()->create();
        $token = VerificationToken::factory()
            ->for($user)
            ->setType(VerificationTokenTypeEnum::EMAIL_VERIFICATION)
            ->create();

        $verifyData = VerifyEmailData::from([
            'token' => $token->token,
        ]);

        $result = VerifyEmailAction::run($verifyData);

        $this->assertTrue($result);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertModelMissing($token);
    }

    public function testVerifyEmailWithInvalidToken(): void
    {
        $user = User::factory()->unverified()->create();
        VerificationToken::factory()
            ->for($user)
            ->setType(VerificationTokenTypeEnum::EMAIL_VERIFICATION)
            ->create();

        $verifyData = VerifyEmailData::from([
            'token' => 'invalid_token',
        ]);

        $result = VerifyEmailAction::run($verifyData);

        $this->assertFalse($result);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function testVerifyEmailWithExpiredToken(): void
    {
        $user = User::factory()->unverified()->create();
        $token = VerificationToken::factory()
            ->for($user)
            ->setType(VerificationTokenTypeEnum::EMAIL_VERIFICATION)
            ->setExpiresAt(now()->subMinutes(30))
            ->create();

        $verifyData = VerifyEmailData::from([
            'token' => $token->token,
        ]);

        $result = VerifyEmailAction::run($verifyData);

        $this->assertFalse($result);
        $this->assertNull($user->fresh()->email_verified_at);
    }
}
