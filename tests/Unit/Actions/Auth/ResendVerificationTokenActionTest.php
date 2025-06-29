<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\ResendVerificationTokenAction;
use App\Data\Actions\Auth\ResendVerificationTokenData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResendVerificationTokenActionTest extends TestCase
{
    use RefreshDatabase;

    public function testResendVerificationTokenWithValidEmail(): void
    {
        $user = User::factory()->unverified()->create();

        $resendData = ResendVerificationTokenData::from([
            'email' => $user->email,
        ]);

        $result = ResendVerificationTokenAction::run($resendData);

        $this->assertTrue($result);
        $this->assertDatabaseHas(VerificationToken::class, [
            'user_id' => $user->id,
            'type' => VerificationTokenTypeEnum::EMAIL_VERIFICATION,
        ]);
    }

    public function testResendVerificationTokenWithInvalidEmail(): void
    {
        $resendData = ResendVerificationTokenData::from([
            'email' => 'nonexistent@example.com',
        ]);

        $result = ResendVerificationTokenAction::run($resendData);

        $this->assertFalse($result);
    }

    public function testResendVerificationTokenWithAlreadyVerifiedEmail(): void
    {
        $user = User::factory()->create();

        $resendData = ResendVerificationTokenData::from([
            'email' => $user->email,
        ]);

        $result = ResendVerificationTokenAction::run($resendData);

        $this->assertFalse($result);
    }
}
