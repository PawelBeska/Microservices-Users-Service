<?php

namespace App\Http\Controllers\Api\v1;

use App\Actions\Auth\ChangePasswordAction;
use App\Actions\Auth\ForgotPasswordAction;
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterAction;
use App\Actions\Auth\ResendVerificationTokenAction;
use App\Actions\Auth\ResetPasswordAction;
use App\Actions\Auth\VerifyEmailAction;
use App\Data\Actions\Auth\ChangePasswordData;
use App\Data\Actions\Auth\ForgotPasswordData;
use App\Data\Actions\Auth\LoginData;
use App\Data\Actions\Auth\RegisterData;
use App\Data\Actions\Auth\ResendVerificationTokenData;
use App\Data\Actions\Auth\ResetPasswordData;
use App\Data\Actions\Auth\VerifyEmailData;
use App\Enums\ResponseCodeEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

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

        return $this->codeResponse();
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken();
    }

    protected function respondWithToken(Authenticatable|User $user): JsonResponse
    {
        return $this->successResponse(
            [
                'user' => $user->toArray(),
                'token' => $user->createToken('auth_token')->accessToken,
            ]
        );
    }

    public function register(RegisterData $data): JsonResponse
    {
        $user = RegisterAction::run($data);

        return $this->respondWithToken($user);
    }

    public function login(LoginData $data): JsonResponse
    {
        if (!LoginAction::run($data)) {
            return $this->codeResponse(ResponseCodeEnum::UNAUTHORIZED);
        }

        if (!Auth::user()->email_verified_at) {
            return $this->codeResponse(ResponseCodeEnum::NOT_VERIFIED_EMAIL);
        }

        return $this->respondWithToken(
            Auth::user()
        );
    }

    public function resendVerificationToken(ResendVerificationTokenData $data): JsonResponse
    {
        if (!ResendVerificationTokenAction::run($data)) {
            return $this->codeResponse(ResponseCodeEnum::NOT_FOUND);
        }

        return $this->codeResponse();
    }

    public function verifyEmail(VerifyEmailData $data): JsonResponse
    {
        if (VerifyEmailAction::run($data)) {
            return $this->successResponse();
        }

        return $this->codeResponse(
            code: ResponseCodeEnum::CODE_INVALID
        );
    }

    public function broadcastingChannel(Request $request): JsonResponse
    {
        $data = $request->all();

        $string = sprintf('%s:%s', $data['socket_id'], $data['channel_name']);

        return response()->json(
            [
                'auth' => config('broadcasting.connections.pusher.key').":".hash_hmac(
                        'sha256',
                        $string,
                        config('broadcasting.connections.pusher.secret')
                    )
            ]
        );
    }

    public function changePassword(ChangePasswordData $data): JsonResponse
    {
        ChangePasswordAction::run($data);

        return $this->successResponse();
    }

    /**
     * @throws Throwable
     */
    public function forgotPassword(ForgotPasswordData $data): JsonResponse
    {
        if (!ForgotPasswordAction::run($data)) {
            return $this->codeResponse(ResponseCodeEnum::NOT_FOUND);
        }

        return $this->successResponse();
    }

    public function resetPassword(ResetPasswordData $data): JsonResponse
    {
        if (!ResetPasswordAction::run($data)) {
            return $this->codeResponse(ResponseCodeEnum::CODE_INVALID);
        }

        return $this->successResponse();
    }
}
