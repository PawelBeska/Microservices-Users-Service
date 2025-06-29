<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\ForgotPasswordAction;
use App\Data\Actions\Auth\ForgotPasswordData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForgotPasswordActionTest extends TestCase
{
    use RefreshDatabase;

    public function testForgotPasswordWithValidEmail(): void
    {
        $user = User::factory()->create();

        $forgotData = ForgotPasswordData::from([
            'email' => $user->email,
        ]);

        $result = ForgotPasswordAction::run($forgotData);

        $this->assertTrue($result);
        $this->assertDatabaseHas(VerificationToken::class, [
            'type' => VerificationTokenTypeEnum::PASSWORD_RESET,
            'user_id' => $user->id,
        ]);
    }

    public function testForgotPasswordWithInvalidEmail(): void
    {
        $forgotData = ForgotPasswordData::from([
            'email' => 'email@example.com',
        ]);

        $result = ForgotPasswordAction::run($forgotData);

        $this->assertFalse($result);
        $this->assertDatabaseCount(VerificationToken::class, 0);
    }
}
