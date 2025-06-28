<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\ChangePasswordData;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Lorisleiva\Actions\Concerns\AsAction;

class ChangePasswordAction
{
    use AsAction;

    public function handle(ChangePasswordData $data, Authenticatable|User $user): void
    {
        $user->password = $data->newPassword;
        $user->save();
    }
}
