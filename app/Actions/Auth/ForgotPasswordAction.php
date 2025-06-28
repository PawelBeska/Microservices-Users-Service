<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\ForgotPasswordData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class ForgotPasswordAction
{
    use AsAction;

    public function handle(ForgotPasswordData $data): bool
    {
        $user = User::query()->where('email', $data->email)->first();
        $token = VerificationToken::query()->create([
            'user_id' => $user->id,
            'token' => Hash::make($user->email),
            'type' => VerificationTokenTypeEnum::PASSWORD_RESET,
            'expires_at' => now()->addHour(),
        ])->token;


        if ($user) {
            $user->sendPasswordResetNotification($token);

            return true;
        }

        return false;
    }
}
