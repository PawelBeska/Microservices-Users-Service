<?php

namespace App\Data\Actions\Auth;

use Spatie\LaravelData\Data;

class VerifyEmailData extends Data
{
    public function __construct(
        public string $email,
        public string $token
    ) {
    }

    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
        ];
    }
}
