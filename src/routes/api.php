<?php

use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Public
// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::prefix('auth')->group(function () {
// });
Route::post('auth/register', RegisterController::class);


// // google
// Route::get("/auth/google", [GoogleController::class, "redirect"]);
// Route::get("/auth/google/callback", [GoogleController::class, "callback"]);

// Route::get("/auth/google/register", [GoogleController::class, "registerRedirect"]);
// Route::get("/auth/google/register/callback", [GoogleController::class, "registerCallback"]);

// Route::get('/test', fn() => response()->json(['ok' => true]));



Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/me', [UserController::class, 'me']);
});


// Route::get('/email/verify/{id}/{token}', function ($id, $token) {
//     $user = User::findOrFail($id);

//     if ($user->email_verified_at) {
//         return response()->json([
//             'message' => 'Email already verified'
//         ], 200);
//     }

//     if (
//         !$user->email_verification_token ||
//         !hash_equals(
//             $user->email_verification_token,
//             hash('sha256', $token)
//         )
//     ) {
//         return response()->json([
//             'message' => 'Invalid or expired verification link'
//         ], 400);
//     }

//     $user->update([
//         'email_verified_at' => now(),
//         'email_verification_token' => null,
//     ]);

//     return redirect('http://localhost:5173/email-verified');
// })->name('verification.verify');


// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/private', fn() => response()->json(['message' => 'private']));
});
