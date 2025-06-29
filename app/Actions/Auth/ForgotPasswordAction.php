<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\ForgotPasswordData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class ForgotPasswordAction
{
    use AsAction;

    public function handle(ForgotPasswordData $data): bool
    {
        try {
            $user = User::query()->where('email', $data->email)->firstOrFail();
            $token = VerificationToken::query()->create([
                'user_id' => $user->id,
                'token' => Hash::make($user->email),
                'type' => VerificationTokenTypeEnum::PASSWORD_RESET,
                'expires_at' => now()->addHour(),
            ])->token;


            if ($user) {
                #!TODO - replace with a proper notification sender

                return true;
            }

            return false;
        } catch (ModelNotFoundException) {
            return false;
        }
    }
}
