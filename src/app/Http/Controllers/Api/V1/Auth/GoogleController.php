<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\AuthService;

class GoogleController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirect(Request $request)
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => $request->type])
            ->redirect();
    }

    /**
     * Handle Google callback
     */
    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $result = $this->authService->googleAuth(
            $googleUser,
            $request->state
        );

        return $this->popup($result);
    }

    /**
     * Return popup script for frontend
     */
    private function popup(array $data)
    {
        $frontendUrl = config('app.frontend_url', '*'); // fallback to '*' if not set

        return response("
            <script>
                window.opener.postMessage(" . json_encode($data) . ", '{$frontendUrl}');
                window.close();
            </script>
        ");
    }
}
