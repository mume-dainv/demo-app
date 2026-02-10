<?php

use App\Http\Middleware\PermissionMiddleware;
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

Route::post('login', [\App\Http\Controllers\Api\Admin\AuthController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\Api\Admin\AuthController::class, 'logout']);
Route::post('refresh', [\App\Http\Controllers\Api\Admin\AuthController::class, 'refresh']);

Route::middleware(['auth:api', 'permission'])->prefix('admin')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\UserController::class, 'index']);
    });
});
