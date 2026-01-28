<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:3',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();


        // if ($request->hasFile('image')) {
        //     $file = $request->file('image');
        //     // Upload to Cloudinary
        //     $uploadedFile = Cloudinary::uploadApi()->upload($file->getRealPath(), [
        //         'folder' => 'users', // optional: store in "users" folder
        //         'transformation' => [
        //             'width' => 300,
        //             'height' => 300,
        //             'crop' => 'fill',
        //             'quality' => 'auto',
        //             'fetch_format' => 'auto',
        //             'gravity'      => 'auto',
        //         ]
        //     ]);
        //     // Get secure URL
        //     $imageUrl = $uploadedFile['secure_url'];

        //     return response()->json([
        //         'message' => 'User registered successfully',
        //         'user'    => $request->all(),
        //         'image'   => $imageUrl,
        //     ], 201);
        // }

        try {
            // 1️⃣ Create user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2️⃣ Upload image if exists
            $imageUrl = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                // Upload to Cloudinary
                $uploadedFile = Cloudinary::uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'users', // optional: store in "users" folder
                    'transformation' => [
                        'width' => 300,
                        'height' => 300,
                        'crop' => 'fill',
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                        'gravity'      => 'auto',
                    ]
                ]);


                $imageUrl = $uploadedFile['secure_url'];

                $user->update(['images' => $imageUrl]);
            }

            // 3️⃣ Create token
            $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'User registered successfully',
                'user'    => $user,
                'token'   => $token,
            ], 201);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user  = Auth::user();

        $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
