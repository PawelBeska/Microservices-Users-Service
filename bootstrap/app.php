<?php

use App\Enums\ResponseCodeEnum;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1'
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptionHandlers = [
            ModelNotFoundException::class => [
                'code' => ResponseCodeEnum::NOT_FOUND->value,
            ],
            NotFoundHttpException::class => [
                'code' => ResponseCodeEnum::NOT_FOUND->value,
            ],
            AccessDeniedHttpException::class => [
                'code' => ResponseCodeEnum::FORBIDDEN->value,
            ],
            ValidationException::class => function (ValidationException $exception) {
                return [
                    'errors' => $exception->errors(),
                    'code' => ResponseCodeEnum::VALIDATION_ERROR->value,
                ];
            },
            ThrottleRequestsException::class => [
                'code' => ResponseCodeEnum::TOO_MANY_REQUESTS->value,
            ],
            AuthorizationException::class => [
                'code' => ResponseCodeEnum::FORBIDDEN->value,
            ],
        ];

        foreach ($exceptionHandlers as $exceptionClass => $response) {
            $exceptions->render(function (Exception $exception) use ($exceptionClass, $response) {
                if ($exception instanceof $exceptionClass) {
                    if (is_array($response)) {
                        return response()->json($response);
                    }

                    return response()->json(
                        $response($exception)
                    );
                }
            });
        }
    })->create();
