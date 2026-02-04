<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $image = ImageService::upload($data['image'] ?? null);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                // 'image' => $image,
            ]);

            return [
                'user' => $user,
                'token' => $user->createToken('api')->plainTextToken,
            ];
        });
    }

    public function login(array $data): array
    {
        if (!Auth::attempt($data)) {
            abort(401, 'Invalid credentials');
        }

        $user = Auth::user();

        return [
            'message' => 'Login successful',
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ];
    }
}
