<?php

namespace App\Http\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3Helper
{
    public static function upload(UploadedFile $file): false|string
    {
        $path = self::generateAvatarPath($file);
        Storage::disk(env('FILESYSTEM_DISK'))->put($path, file_get_contents($file));
        return $path;

    }

    public static function delete(string $path): void
    {
        Storage::disk(env('FILESYSTEM_DISK'))->delete($path);
    }

    public static function getUrl($path): string
    {
        return Storage::disk(env('FILESYSTEM_DISK'))->temporaryUrl($path, now()->addMinutes(5));
    }

    private static function generateAvatarPath(UploadedFile $file): string
    {
        return 'users/avatars/' . time() . '.' . $file->getClientOriginalExtension();
    }
}
