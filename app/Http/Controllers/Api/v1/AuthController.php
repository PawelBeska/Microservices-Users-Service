<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ResponseCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\v1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\v1\Auth\LoginRequest;
use App\Http\Requests\Api\v1\Auth\RegisterRequest;
use App\Http\Requests\Api\v1\Auth\ResendVerificationTokenRequest;
use App\Http\Requests\Api\v1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\v1\Auth\VerifyEmailRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpFoundationRedirectResponse;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->loadMissing(['roles', 'permissions', 'roles.permissions']);

        return $this->successResponse(
            $user
        );
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return $this->successResponse(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken();
    }

    protected function respondWithToken(): JsonResponse
    {
        return response()->json(
            [
                'access_token' =>
                    [
                        'aud' => 'http://localhost:8080',
                        'sub' => (string)auth()->user()->id,
                        'iss' => 'http://auth/api/auth/login',
                        'jti' => Str::uuid(),
                    ]
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $userDto = $request->toDto();

        return $this->successResponse();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!auth()->attempt($credentials)) {
            return $this->codeResponse(ResponseCodeEnum::UNAUTHORIZED);
        }

        if (!auth()->user()?->hasVerifiedEmail()) {
            return $this->codeResponse(ResponseCodeEnum::NOT_VERIFIED_EMAIL);
        }

        return $this->respondWithToken();
    }

    public function resendVerificationToken(ResendVerificationTokenRequest $request): JsonResponse
    {
        if (($user = $this->userRepository->getByEmail($request->getEmail())) && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return $this->successResponse();
        }

        return $this->codeResponse(ResponseCodeEnum::NOT_FOUND);
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        if (($user = $this->userRepository->getByEmail($request->get('email'))) && !$user->hasVerifiedEmail()) {
            try {
                $this->userService->setUser($user)->verifyEmail($request->get('code'));

                return $this->successResponse();
            } catch (CodeInvalidException) {
                return $this->codeResponse(
                    code: ResponseCodeEnum::CODE_INVALID
                );
            }
        }

        return $this->errorResponse(code: ResponseCodeEnum::NOT_FOUND);
    }

    public function broadcastingChannel(Request $request): JsonResponse
    {
        $data = $request->all();

        $string = sprintf('%s:%s', $data['socket_id'], $data['channel_name']);

        return response()->json(
            [
                'auth' => config('broadcasting.connections.pusher.key') . ":" . hash_hmac(
                        'sha256',
                        $string,
                        config('broadcasting.connections.pusher.secret')
                    )
            ]
        );
    }

    public function externalAuthAccountLogin(ExternalAuthProviderNameEnum $resolver
    ): HttpFoundationRedirectResponse|JsonResponse {
        return $this->successResponse([
            'auth_url' => $this->externalAuthResolver->initiate($resolver)->auth()->getTargetUrl()
        ]);
    }

    public function externalAuthAccountCallback(ExternalAuthProviderNameEnum $resolver): JsonResponse|RedirectResponse
    {
        $user = $this->externalAuthResolver->initiate($resolver)->callback();

        return Redirect::to(
            route('auth.external.callback', [
                'token' => JWTAuth::fromUser($user)
            ])
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->userService->setUser(Auth::user())->setPassword($request->get('new_password'), true);

        return $this->successResponse();
    }

    /**
     * @throws Throwable
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        Verification::make()->checkBlacklist(
            UserDataEnum::EMAIL,
            $request->get('email')
        );

        if (!$user = $this->userRepository->getByEmail($request->get('email'))) {
            return $this->codeResponse(ResponseCodeEnum::NOT_FOUND);
        }

        $token = $this->userService->setUser($user)->createVerificationCode(
            VerificationCodeTypeEnum::PASSWORD_RESET, [
                'email' => $request->get('email'),
                'user_id' => $user->id,
            ]
        )->getVerificationCode();

        Mail::to($request->get('email'))->send(new ForgotPassword($token));

        return $this->successResponse();
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        if (!$this->verificationCodeRepository->checkUnusedCodeExists(
            VerificationCodeTypeEnum::PASSWORD_RESET,
            $request->get('token')
        )) {
            return $this->codeResponse(ResponseCodeEnum::CODE_INVALID);
        }

        $tokenData = $request->getTokenData();

        $user = $this->userRepository->getByEmailAndId(
            data_get($tokenData, 'user_id'),
            data_get($tokenData, 'email')
        );

        $this->userService->setUser($user)->setPassword($request->get('new_password'), true);

        return $this->successResponse();
    }


}
