<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\RegisterAction;
use App\Data\Actions\Auth\RegisterData;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterActionTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterCreatesUserSuccessfully(): void
    {
        $registerData = RegisterData::from([
            'name' => 'name',
            'email' => 'email@example.com',
            'password' => 'password',
        ]);

        $user = RegisterAction::run($registerData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'name' => $registerData->name,
            'email' => $registerData->email,
        ]);
        $this->assertTrue(Hash::check($registerData->password, $user->password));
        $this->assertNull($user->email_verified_at);
    }

    public function testRegisterWithExistingEmailFails(): void
    {
        User::factory()
            ->create(['email' => 'email@example.com']);

        $registerData = RegisterData::from([
            'name' => 'name',
            'email' => 'email@example.com',
            'password' => 'password'
        ]);

        $this->expectException(Exception::class);
        RegisterAction::run($registerData);
    }
}
