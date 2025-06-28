<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\LoginData;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class LoginAction
{
    use AsAction;

    public function handle(LoginData $data): bool
    {
        if (Auth::attempt($data->toArray())) {
            return true;
        }

        return false;
    }
}
