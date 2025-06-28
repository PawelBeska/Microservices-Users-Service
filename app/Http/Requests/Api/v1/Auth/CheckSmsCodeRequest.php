<?php

namespace App\Http\Requests\Api\v1\Auth;

use App\Enums\VerificationCodeTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CheckSmsCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sms_code' => ['required', 'numeric'],
            'type' => ['required', 'string', new Enum(VerificationCodeTypeEnum::class)],
        ];
    }


    public function getType(): VerificationCodeTypeEnum
    {
        return VerificationCodeTypeEnum::from($this->get('type'));
    }
}
