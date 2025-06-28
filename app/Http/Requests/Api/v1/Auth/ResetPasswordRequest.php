<?php

namespace App\Http\Requests\Api\v1\Auth;

use App\Facades\ExtendedEncrypter;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'new_password' => ['required', 'string', new PasswordRule()],
        ];
    }

    public function getTokenData(): array
    {
        return ExtendedEncrypter::asArray($this->get('token'));
    }
}
