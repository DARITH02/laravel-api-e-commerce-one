<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\V1\Category\CategoryController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public Auth Routes
|--------------------------------------------------------------------------
*/
// Route::post('auth/login', [AuthController::class, 'login']);
// Route::post('auth/register', RegisterController::class);

/*
|--------------------------------------------------------------------------
| Google OAuth Routes
|--------------------------------------------------------------------------
*/ Route::prefix('auth/google')->group(function () {
    Route::get('redirect', [GoogleController::class, 'redirect']); // or 'redirect' if you named it that
    Route::get('callback', [GoogleController::class, 'callback']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [UserController::class, 'me']);
    Route::get('private', fn () => response()->json(['message' => 'private']));
});

Route::get('/category',[CategoryController::class,'index']);