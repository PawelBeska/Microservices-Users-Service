<?php

namespace App\Http\Requests\Api\v1\Auth;

use App\ValueObjects\EncryptedTokenFromUserDataVO;
use Illuminate\Foundation\Http\FormRequest;
use JsonException;

class VerifyEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
        ];
    }

    /**
     * @throws JsonException
     */
    public function getTokenData(): array
    {
        return EncryptedTokenFromUserDataVO::make(
            $this->get('token')
        )->decryptToken();
    }
}
