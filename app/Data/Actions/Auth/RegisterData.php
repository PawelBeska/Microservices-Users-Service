<?php

namespace App\Data\Actions\Auth;

use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }

    public static function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ]
        ];
    }
}
