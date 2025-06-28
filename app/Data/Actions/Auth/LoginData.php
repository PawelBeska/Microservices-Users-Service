<?php

namespace App\Data\Actions\Auth;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }

    public static function rules(ValidationContext $context = null): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'string',
                Password::required()
            ],
        ];
    }
}
