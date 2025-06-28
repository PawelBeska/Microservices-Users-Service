<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\VerifyEmailData;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyEmailAction
{
    use AsAction;

    public function handle(VerifyEmailData $data): bool
    {
        $user = User::query()->where('email', $data->email)->first();
        #!TODO - Check if the token is valid


        if ($user?->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            return true;
        }

        return false;
    }

}
