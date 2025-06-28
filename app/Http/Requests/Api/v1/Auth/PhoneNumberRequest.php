<?php

namespace App\Http\Requests\Api\v1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PhoneNumberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'digits:9'],
        ];
    }
}
