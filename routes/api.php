<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
Route::post('refresh', [\App\Http\Controllers\Api\AuthController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [\App\Http\Controllers\Api\ProfileController::class, 'profile']);
    Route::post('me', [\App\Http\Controllers\Api\ProfileController::class, 'updateProfile']);
});

Route::middleware(['auth:api', 'permission'])->prefix('admin')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Admin\UserController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'delete']);
    });
});
