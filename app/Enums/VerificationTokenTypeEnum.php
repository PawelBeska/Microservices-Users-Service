<?php

namespace App\Enums;

enum VerificationTokenTypeEnum: string
{
    case EMAIL_VERIFICATION = 'email_verification';
    case PASSWORD_RESET = 'password_reset';
}
