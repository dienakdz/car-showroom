<?php

namespace App\Services\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CarMediaService
{
    /**
     * Crop to 16:9 aspect ratio, resize to standard 1280x720, and store to storage disk.
     */
    public function processAndStore(UploadedFile $file, string $folder = 'inventory-media', string $disk = 'public'): ?string
    {
        $realPath = $file->getRealPath();

        if (! $realPath || ! file_exists($realPath) || ! extension_loaded('gd')) {
            $rawPath = $file->store($folder, $disk);

            return is_string($rawPath) ? $rawPath : null;
        }

        $imageContent = file_get_contents($realPath);
        if ($imageContent === false) {
            $rawPath = $file->store($folder, $disk);

            return is_string($rawPath) ? $rawPath : null;
        }

        $source = @imagecreatefromstring($imageContent);
        if (! $source) {
            $rawPath = $file->store($folder, $disk);

            return is_string($rawPath) ? $rawPath : null;
        }

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        $targetWidth = config('showroom.media.max_width');
        $targetHeight = config('showroom.media.max_height');
        $jpegQuality = config('showroom.media.jpeg_quality');
        $targetAspect = $targetHeight > 0 ? ($targetWidth / $targetHeight) : (16 / 9);

        $origAspect = $origWidth / $origHeight;

        if ($origAspect > $targetAspect) {
            // Wider than target ratio -> crop sides
            $cropWidth = (int) round($origHeight * $targetAspect);
            $cropHeight = $origHeight;
            $cropX = (int) max(0, round(($origWidth - $cropWidth) / 2));
            $cropY = 0;
        } elseif ($origAspect < $targetAspect) {
            // Taller than target ratio -> crop top/bottom
            $cropWidth = $origWidth;
            $cropHeight = (int) round($origWidth / $targetAspect);
            $cropX = 0;
            $cropY = (int) max(0, round(($origHeight - $cropHeight) / 2));
        } else {
            $cropWidth = $origWidth;
            $cropHeight = $origHeight;
            $cropX = 0;
            $cropY = 0;
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if (! $canvas) {
            imagedestroy($source);
            $rawPath = $file->store($folder, $disk);

            return is_string($rawPath) ? $rawPath : null;
        }

        // Fill white background (useful for transparent PNGs)
        $white = imagecolorallocate($canvas, 255, 255, 255);
        if ($white !== false) {
            imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $white);
        }

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        $filename = trim($folder, '/') . '/' . Str::random(40) . '.jpg';
        $storageDir = Storage::disk($disk)->path(trim($folder, '/'));

        if (! is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $targetFullPath = Storage::disk($disk)->path($filename);
        $saved = imagejpeg($canvas, $targetFullPath, $jpegQuality);

        imagedestroy($source);
        imagedestroy($canvas);

        if ($saved) {
            return $filename;
        }

        $rawPath = $file->store($folder, $disk);

        return is_string($rawPath) ? $rawPath : null;
    }
}
