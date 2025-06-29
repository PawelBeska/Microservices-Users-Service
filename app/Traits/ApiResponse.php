<?php

namespace App\Traits;

use App\Enums\ResponseCodeEnum;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

trait ApiResponse
{
    public function successResponse(mixed $data = [], ResponseCodeEnum $code = ResponseCodeEnum::OK): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'code' => $code
        ]);
    }

    public function errorResponse(
        mixed $data = [],
        ResponseCodeEnum $code = ResponseCodeEnum::BAD_REQUEST,
        int $httpCode = HttpFoundationResponse::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json(
            [
                'data' => $data,
                'code' => $code->value
            ],
            $httpCode
        );
    }

    public function codeResponse(ResponseCodeEnum $code = ResponseCodeEnum::OK): JsonResponse
    {
        return response()->json(
            [
                'code' => $code->value
            ],
            $code->httpCode()
        );
    }

    public function validationResponse(mixed $data = []): JsonResponse
    {
        return response()->json(
            [
                'errors' => $data,
                'code' => ResponseCodeEnum::VALIDATION_ERROR->value
            ]
        );
    }
}
