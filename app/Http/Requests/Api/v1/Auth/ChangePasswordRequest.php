<?php

namespace App\Http\Requests\Api\v1\Auth;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', new PasswordRule()],
        ];
    }
}
