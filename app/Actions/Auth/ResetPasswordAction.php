<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\ResetPasswordData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\VerificationToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetPasswordAction
{
    use AsAction;

    public function handle(ResetPasswordData $data): bool
    {
        try {
            /** @var VerificationToken $token */
            $token = VerificationToken::isValid($data->token, VerificationTokenTypeEnum::PASSWORD_RESET)->firstOrFail();
            $user = $token->user;

            if (!$user) {
                return false;
            }

            $user->password = $data->password;
            $user->save();

            $token->delete();

            return true;
        } catch (ModelNotFoundException) {
            return false;
        }
    }
}
