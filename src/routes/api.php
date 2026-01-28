<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

Route::get('/check-cloudinary-fixed', function() {
    try {
        // Get config values
        $config = config('cloudinary');
        
        // Check if we can parse the URL
        $cloudUrl = $config['cloud_url'] ?? null;
        $parsed = parse_url($cloudUrl);
        
        // Try to create Cloudinary instance
        $cloudinary = app('cloudinary');
        
        return response()->json([
            'success' => true,
            'config' => [
                'cloud_url' => $cloudUrl,
                'parsed_url' => $parsed,
                'has_scheme' => isset($parsed['scheme']) && $parsed['scheme'] === 'cloudinary',
                'has_user' => isset($parsed['user']),
                'has_pass' => isset($parsed['pass']),
                'has_host' => isset($parsed['host']),
            ],
            'env_vars' => [
                'CLOUDINARY_CLOUD_NAME' => env('CLOUDINARY_CLOUD_NAME'),
                'CLOUDINARY_API_KEY' => env('CLOUDINARY_API_KEY'),
                'CLOUDINARY_API_SECRET' => env('CLOUDINARY_API_SECRET'),
                'CLOUDINARY_URL' => env('CLOUDINARY_URL'),
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'config_file_content' => file_get_contents(config_path('cloudinary.php')),
            'suggestion' => 'Make sure the config file is using the correct env variable names'
        ], 500);
    }
});
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
