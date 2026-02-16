<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AuthRequest;
use App\Http\Resources\ProfileResource;
use App\Repositories\UserLoggingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseApiController
{
    public function __construct(protected UserLoggingRepository $UserLoggingRepository)
    {
    }

    /**
     * @throws \Throwable
     */
    public function login(AuthRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $tokenResult = auth()->attempt($data);

            $key =  $this->throttleKey($request);

            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);

                return $this->sendErrorResponse([], "Too many attempts! Try after {$seconds} seconds.");
            }

            if (!$tokenResult) {
                RateLimiter::hit($key, 300);
                return $this->sendErrorResponse($data, "Invalid credentials");
            }

            $user = auth()->user();

            $this->UserLoggingRepository->createOrUpdateByUserId([
                'user_id' => $user['id'],
                'ip' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
            ]);
            RateLimiter::clear($key);
            DB::commit();
            return $this->sendResponseWithCookie(['user' => new ProfileResource($user)], ['token',
                $tokenResult,
                env('JWT_TTL'),
                '/',
                null,
                true,         // secure (https production)
                true,         // httpOnly
                false,
                'Lax' ] ,'User logged in successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->sendResponseWithCookie([],['token', null, -1, '/', null, true, true, false, 'Lax'],'User logout successfully.');
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e);
        }
    }

    public function refresh()
    {
        try {
            $token = auth()->refresh();
            return $this->sendResponseWithCookie([], ['token',
                $token,
                env('JWT_TTL'),
                '/',
                null,
                true,         // secure (https production)
                true,         // httpOnly
                false,
                'Strict' ],'Refresh Token successfully.');
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e,"token refresh failed");
        }
    }

    protected function throttleKey($request)
    {
        return Str::lower($request->email).'|'.$request->ip();
    }
}
