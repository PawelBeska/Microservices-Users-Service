<?php

namespace App\Data\Actions\Auth;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;

class ResetPasswordData extends Data
{
    public function __construct(
        public string $token,
        public string $password
    ) {
    }

    public static function rules(): array
    {
        return [
            'token' => ['required'],
            'password' => ['required', 'string', Password::required()],
        ];
    }
}
