<?php

namespace App\Actions\Auth;

use App\Data\Actions\Auth\RegisterData;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterAction
{
    use AsAction;

    public function handle(RegisterData $data): User
    {
        return User::query()->create(
            $data->toArray()
        );
    }
}
