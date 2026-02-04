<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Google\Client;
use Google\Service\Gmail as GmailService;
use Google\Service\Gmail\Message as GmailMessage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|min:3',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // -------------------------
            // Create user
            // -------------------------
            $user = User::create([
                'name'  => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verification_token' => Str::random(64),
            ]);

            // -------------------------
            // Verification link
            // -------------------------
            $verificationLink = route('verification.verify', [
                'id'    => $user->id,
                'token' => $user->email_verification_token,
            ]);

            // -------------------------
            // Gmail API Client
            // -------------------------
            $client = new Client();
            $client->setApplicationName('Laravel Gmail API');
            $client->setScopes(GmailService::GMAIL_SEND);
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->setAccessType('offline');

            // // Optional: cache tokens
            // $client->setCache(
            //     new \Google\Auth\Cache\FileSystemCache(storage_path('framework/cache/google'))
            // );

            $tokenPath = storage_path('app/google/token.json');

            if (file_exists($tokenPath)) {
                $client->setAccessToken(json_decode(file_get_contents($tokenPath), true));
            }

            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                } else {
                    throw new \Exception('Gmail refresh token missing');
                }
            }

            $gmail = new GmailService($client);

            // -------------------------
            // Prepare email
            // -------------------------
            $rawMessage = "From: me\r\n";
            $rawMessage .= "To: {$user->email}\r\n";
            $rawMessage .= "Subject: Verify your email\r\n\r\n";
            $rawMessage .= "Hello {$user->name},\n\n";
            $rawMessage .= "Click this link to verify your email:\n{$verificationLink}";

            $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');

            $message = new GmailMessage();
            $message->setRaw($encodedMessage);

            $gmail->users_messages->send('me', $message);

            // -------------------------
            // API Token
            // -------------------------
        $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Registered successfully. Check your email.',
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
     * REGISTER

    
     */
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|confirmed|min:3',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         // 1️⃣ Create user
    //         $user = User::create([
    //             'name'     => $request['name'],
    //             'email'    => $request['email'],
    //             'password' => Hash::make($request['password']),
    //         ]);

    //         // 2️⃣ Upload image if exists
    //         $imageUrl = null;
    //         if ($request->hasFile('image') && $request->file('image')->isValid()) {
    //             $file = $request->file('image');
    //             // Upload to Cloudinary
    //             $uploadedFile = Cloudinary::uploadApi()->upload($file->getRealPath(), [
    //                 'folder' => 'users', // optional: store in "users" folder
    //                 'transformation' => [
    //                     'width' => 300,
    //                     'height' => 300,
    //                     'crop' => 'fill',
    //                     'quality' => 'auto',
    //                     'fetch_format' => 'auto',
    //                     'gravity'      => 'auto',
    //                 ]
    //             ]);
    //             $imageUrl = $uploadedFile['secure_url'];

    //             $user->update(['images' => $imageUrl]);
    //         }
    //         $user->sendEmailVerificationNotification();

    //         // 3️⃣ Create token
    //         $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'User registered successfully',
    //             'user'    => $user,
    //             'token'   => $token,
    //         ], 201);
    //     } catch (\Throwable $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'message' => 'Registration failed',
    //             'error'   => $e->getMessage(),
    //         ], 500);
    //     }
    // }


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
