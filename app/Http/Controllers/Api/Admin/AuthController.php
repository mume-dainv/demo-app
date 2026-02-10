<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseApiController
{
    public function login(AuthRequest $request)
    {
        $data = $request->validated();
        try {

            $tokenResult = auth()->attempt($data);

            $key =  $this->throttleKey($request);

            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);

                return $this->sendErrorResponse([], "Too many attempts! try after {$seconds} seconds.");
            }

            if (!$tokenResult) {
                RateLimiter::hit($key, 300);
                return $this->sendErrorResponse($data, "Invalid credentials");
            }

            $user = auth()->user();
            RateLimiter::clear($key);
            return $this->sendResponse(['user' => $user, 'access_token' => $tokenResult], 'User logged in successfully.');
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->sendResponse([],'User logout successfully.');
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e);
        }
    }

    public function refresh()
    {
        try {
            $token = auth()->refresh();
            return $this->sendResponse(['token' => $token], 'Refresh Token successfully.');
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e,"token refresh failed");
        }
    }

    protected function throttleKey($request)
    {
        return Str::lower($request->email).'|'.$request->ip();
    }
}
