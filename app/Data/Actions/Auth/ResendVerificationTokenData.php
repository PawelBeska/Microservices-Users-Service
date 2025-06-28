<?php

namespace App\Data\Actions\Auth;

use Spatie\LaravelData\Data;

class ResendVerificationTokenData extends Data
{
    public function __construct(
        public string $email
    ) {
    }

    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}
