<?php

namespace App\Http\Requests\Api\v1\Auth;

use App\Interfaces\Requests\RequestToValueObject;
use App\ValueObjects\EncryptedTokenFromUserDataVO;
use Illuminate\Foundation\Http\FormRequest;
use JsonException;

class SendMailVerificationLinkRequest extends FormRequest implements RequestToValueObject
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'payload' => ['required', 'array'],
        ];
    }

    /**
     * @throws JsonException
     */
    public function toValueObject(): EncryptedTokenFromUserDataVO
    {
        return EncryptedTokenFromUserDataVO::from($this->get('payload', []));
    }
}
