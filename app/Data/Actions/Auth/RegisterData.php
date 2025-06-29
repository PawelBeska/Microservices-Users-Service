<?php

namespace App\Data\Actions\Auth;

use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }

    public static function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:32',
            ],
            'email' => [
                'required',
                'unique:users,email',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ]
        ];
    }
}
