<?php

namespace App\Http\Requests\Api\v1\Auth;

use App\Dto\Users\UserDto;
use App\Enums\UserStatusEnum;
use App\Interfaces\Requests\RequestToDtoInterface;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest implements RequestToDtoInterface
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where('status', UserStatusEnum::VERIFIED),
            ],
            'password' => [
                'required',
                'string',
                new PasswordRule(),
            ],
        ];
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            email: $this->request->get('email'),
            status: UserStatusEnum::GUEST
        );
    }
}
