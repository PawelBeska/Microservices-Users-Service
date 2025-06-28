<?php

namespace App\Data\Actions\Auth;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ChangePasswordData extends Data
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
    ) {
    }

    public static function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', Password::required()],
        ];
    }
}
