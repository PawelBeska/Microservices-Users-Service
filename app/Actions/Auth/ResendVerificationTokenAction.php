<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\ResendVerificationTokenData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class ResendVerificationTokenAction
{
    use AsAction;

    public function handle(ResendVerificationTokenData $data): bool
    {
        try {
            $user = User::query()
                ->where('email', $data->email)
                ->whereNull('email_verified_at')
                ->firstOrFail();

            $token = VerificationToken::query()->create([
                'user_id' => $user->id,
                'token' => Hash::make($user->email),
                'type' => VerificationTokenTypeEnum::EMAIL_VERIFICATION,
                'expires_at' => now()->addMonth(),
            ])->token;

            if (!$user?->hasVerifiedEmail()) {
                #!TODO - replace with a proper notification sender

                return true;
            }

            return false;
        } catch (ModelNotFoundException) {
            return false;
        }
    }
}
