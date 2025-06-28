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

    case POST_LIMIT_EXCEEDED = 1002;
    case PHONE_NUMBER_ALREADY_VERIFIED = 1003;
    case EMAIL_ALREADY_VERIFIED = 1004;
    case DATA_ON_BLACKLIST = 1005;

}
