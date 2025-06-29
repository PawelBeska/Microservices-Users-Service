<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\VerifyEmailData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\VerificationToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyEmailAction
{
    use AsAction;

    public function handle(VerifyEmailData $data): bool
    {
        try {
            /** @var VerificationToken $verificationToken */
            $verificationToken = VerificationToken::isValid($data->token, VerificationTokenTypeEnum::EMAIL_VERIFICATION)->firstOrFail();
            $user = $verificationToken->user;

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();

                $verificationToken->delete();
                return true;
            }

            return false;
        } catch (ModelNotFoundException) {
            return false;
        }
    }

}
