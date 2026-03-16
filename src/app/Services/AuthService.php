<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Throwable;

class AuthService
{
    public function googleAuth($googleUser, string $type): array
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $googleUser->getEmail())->first();

            /** LOGIN */
            if ($type === 'login') {
                if (!$user) {
                    return [
                        'status' => 'not_registered',
                        'message' => 'Please register first',
                    ];
                }
                return [
                    'status' => 'login_success',
                    'user' => $user,
                    'token' => $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken,
                    'message' => 'Login successful',
                ];
            }

            /** REGISTER */
            if ($type === 'register') {
                if ($user) {
                    return [
                        'status' => 'already_registered',
                        'message' => 'You are already registered. Please login.',
                    ];
                }

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'provider' => 'google',
                    'images' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(40)),
                    'email_verified_at' => now(),
                ]);

                DB::commit();

                return [
                    'status' => 'register_success',
                    'user' => $user,
                    'token' => $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken,
                    'message' => 'Registration successful',
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Invalid request type',
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Google authentication failed',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}
