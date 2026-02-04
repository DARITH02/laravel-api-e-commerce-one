<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageService
{
    public static function upload(?UploadedFile $file, string $path = 'profiles'): ?string
    {
        if (!$file) return null;

        return $file->store($path, 'public');
    }
}
