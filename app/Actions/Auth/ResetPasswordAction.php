<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\ResetPasswordData;
use App\Enums\VerificationTokenTypeEnum;
use App\Models\User;
use App\Models\VerificationToken;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetPasswordAction
{
    use AsAction;

    public function handle(ResetPasswordData $data): bool
    {
        /** @var User $user */
        #!TODO implement user lookup by password reset token
        $user = VerificationToken::isValid($data->token, VerificationTokenTypeEnum::PASSWORD_RESET)->first()?->user;

        if (!$user) {
            return false;
        }

        $user->password = $data->password;
        $user->save();

        return true;
    }
}
