<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect to Google
     * type = login | register
     */
    public function redirect(Request $request)
    {
        return Socialite::driver('google')
            ->stateless()
            ->with([
                'state' => $request->type // pass intent
            ])
            ->redirect();
    }

    /**
     * Google callback
     */
    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $type = $request->state; // login | register

        $user = User::where('email', $googleUser->getEmail())->first();

        /** ================= LOGIN ================= */
        if ($type === 'login') {
            if (!$user) {
                return $this->popup([
                    'status' => 'not_registered',
                    'message' => 'Please register first!'
                ]);
            }

            return $this->popup([
                'status' => 'login_success',
                'user' => $user,
                'token' => $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken,
                'message' => 'Login successful!'
            ]);
        }

        /** ================= REGISTER ================= */
        if ($type === 'register') {
            if ($user) {
                return $this->popup([
                    'status' => 'already_registered',
                    'message' => 'You are already registered. Please login!'
                ]);
            }

            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(Str::random(16)),
                'email_verified_at' => now(),
                'provider' => 'google',
                'images' => $googleUser->getAvatar(),
            ]);

            return $this->popup([
                'status' => 'register_success',
                'user' => $user,
                'token' => $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken,
                'message' => 'Registration successful!'
            ]);
        }

        // fallback
        return $this->popup([
            'status' => 'error',
            'message' => 'Invalid request'
        ]);
    }

    /**
     * Send data back to frontend popup
     */
    private function popup(array $data)
    {
        return response("
        <script>
            window.opener.postMessage(" . json_encode($data) . ", 'http://localhost:5173');
            window.close();
        </script>
        ");
    }










    /*
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
|||||||||||||||||||
*/

















    //     public function redirect()
    //     {
    //         return Socialite::driver('google')
    //             ->stateless()
    //             ->redirect();

    //         // dd(config('services.google.redirect'));
    //     }
    //     // public function callback()
    //     // {
    //     //     $googleUser = Socialite::driver('google')->stateless()->user();

    //     //     $user = User::where('email', $googleUser->getEmail())->first();
    //     //     if ($user) {
    //     //         $status = "Not reigster!";
    //     //         $userData = json_encode([
    //     //             'user' => null,
    //     //             'token' => null,
    //     //             'status' => $status,
    //     //         ]);
    //     //         return response("
    //     //         <script>
    //     //             window.opener.postMessage($userData, 'http://localhost:5173');
    //     //             window.close();
    //     //         </script>
    //     //     ");
    //     //     }


    //     //     $user = User::updateOrCreate(
    //     //         ['email' => $googleUser->getEmail()],
    //     //         [
    //     //             'name' => $googleUser->getName(),
    //     //             'google_id' => $googleUser->getId(),
    //     //             'password' => bcrypt(Str::random(16)),
    //     //             'email_verified_at' => now(),
    //     //             'provider' => 'google',
    //     //             'images' => $googleUser->getAvatar(),
    //     //         ]
    //     //     );
    //     //     $token = $user->createToken('api')->plainTextToken;


    //     //     // return response()->json([
    //     //     //     'message' => 'User created successfully',
    //     //     //     'user' => $user,
    //     //     //     'token' => $token,
    //     //     // ]);
    //     //     // Send the user + token to the opener window via JS
    //     //     $userData = json_encode([
    //     //         'user' => $user,
    //     //         'token' => $token
    //     //     ]);
    //     //     return response("
    //     //     <script>
    //     //         window.opener.postMessage($userData, 'http://localhost:5173');
    //     //         window.close();
    //     //     </script>
    //     // ");
    //     //}



    //     public function callback()
    //     {
    //         $googleUser = Socialite::driver('google')->stateless()->user();

    //         // Check if user exists
    //         $user = User::where('email', $googleUser->getEmail())->first();

    //         if (!$user) {
    //             // User NOT registered → cannot login
    //             return response("
    // <script>
    //     window.opener.postMessage(" . json_encode([
    //                 'user' => null,
    //                 'token' => null,
    //                 'status' => 'not_registered',
    //                 'message' => 'You need to register first!'
    //             ]) . ", 'http://localhost:5173');
    //     window.close();
    // </script>
    // ");
    //         }

    //         // User exists → login success
    //         $token = $user->createToken('api')->plainTextToken;

    //         return response("
    // <script>
    //     window.opener.postMessage(" . json_encode([
    //             'user' => $user,
    //             'token' => $token,
    //             'status' => 'login_success',
    //             'message' => 'Login successful!'
    //         ]) . ", 'http://localhost:5173');
    //     window.close();
    // </script>
    // ");
    //     }

    //     public function registerRedirect()
    //     {
    //         return Socialite::driver("google")->stateless()->redirect();
    //     }

    //     public function registerCallback()
    //     {
    //         $googleUser = Socialite::driver("google")->stateless()->user();

    //         $user = User::where('email', $googleUser->getEmail())->first();
    //         if ($user) {
    //             return response("
    // <script>
    //     window.opener.postMessage(" . json_encode([
    //                 'user' => null,
    //                 'token' => null,
    //                 'status' => 'already_registered',
    //                 'message' => 'You are already registered! Please login instead.'
    //             ]) . ", 'http://localhost:5173');
    //     window.close();
    // </script>
    // ");
    //         }

    //         $user = User::create([
    //             'name' => $googleUser->getName(),
    //             'email' => $googleUser->getEmail(),
    //             'google_id' => $googleUser->getId(),
    //             'password' => bcrypt(Str::random(16)),
    //             'email_verified_at' => now(),
    //             'provider' => 'google',
    //             'images' => $googleUser->getAvatar(),
    //         ]);

    //         $token = $user->createToken('api')->plainTextToken;
    //         return response("
    // <script>
    //     window.opener.postMessage(" . json_encode([
    //             'user' => $user,
    //             'token' => $token,
    //             'status' => 'register_success',
    //             'message' => 'Registration successful!'
    //         ]) . ", 'http://localhost:5173');
    //     window.close();
    // </script>
    // ");
    //     }
}
