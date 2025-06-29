<?php

namespace App\Data\Actions\Auth;

use Spatie\LaravelData\Data;

class VerifyEmailData extends Data
{
    public function __construct(
        public string $token
    ) {
    }

    public static function rules(): array
    {
        return [
            'token' => ['required', 'string'],
        ];
    }
}
