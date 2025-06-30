<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\LoginAction;
use App\Data\Actions\Auth\LoginData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginWithValidCredentials(): void
    {
        $user = User::factory()
            ->create();

        $loginData = LoginData::from([
            'email' => $user->email,
            'password' => 'password',
        ]);

        $result = LoginAction::run($loginData);

        $this->assertNotFalse($result);
        $this->assertAuthenticatedAs($user);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $user = User::factory()->create();

        $loginData = LoginData::from([
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $result = LoginAction::run($loginData);

        $this->assertFalse($result);
        $this->assertGuest();
    }

    public function testLoginWithNonExistentUser(): void
    {
        $loginData = LoginData::from([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $result = LoginAction::run($loginData);

        $this->assertFalse($result);
        $this->assertGuest();
    }
}
