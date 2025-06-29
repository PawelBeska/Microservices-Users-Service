<?php

namespace App\Enums;

use App\Traits\Equatable;

enum ResponseCodeEnum: int
{
    use Equatable;

    case OK = 200;
    case BAD_REQUEST = 400;

    case UNAUTHORIZED = 401;

    case FORBIDDEN = 403;

    case NOT_FOUND = 404;

    case VALIDATION_ERROR = 422;
    case TOO_MANY_REQUESTS = 429;

    case EXCEPTION = 500;
    case CODE_INVALID = 1000;

    case NOT_VERIFIED_EMAIL = 1001;

    public function httpCode(): int
    {
        return match ($this) {
            self::OK => 200,
            self::BAD_REQUEST, self::NOT_VERIFIED_EMAIL, self::CODE_INVALID => 400,
            self::UNAUTHORIZED => 401,
            self::FORBIDDEN => 403,
            self::NOT_FOUND => 404,
            self::VALIDATION_ERROR => 422,
            self::TOO_MANY_REQUESTS => 429,
            self::EXCEPTION => 500,
        };
    }

}
