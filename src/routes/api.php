<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get("/products", function () {
    return response()->json([
        "message" => "products"
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/private', function () {
        return response()->json([
            'message' => 'private'
        ]);
    });
});
