<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Jose\Component\KeyManagement\JWKFactory;

class TokenController extends Controller
{

    public function token(): JsonResponse
    {
        $jwk = JWKFactory::createFromSecret(
            config('jwt.secret'),
            [
                'alg' => 'HS256',
                'use' => 'sig'
            ]
        );

        return response()->json(
            [
                'keys' =>
                    [
                        $jwk
                    ]
            ]
        );
    }
}
