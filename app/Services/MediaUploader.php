<?php

namespace App\Services;

use App\Models\Setting;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * MediaUploader Service - Configurable Image Processing
 * 
 * Features:
 * - Settings-based configuration (thumbnail sizes, quality, etc.)
 * - Auto-converts images to WebP format (if enabled)
 * - Configurable compression quality
 * - Multiple thumbnail sizes (customizable via settings)
 */
class MediaUploader
{
    protected ImageManager $manager;
    protected array $sizes = [];
    protected int $webpQuality;
    protected bool $generateThumbnails;
    protected bool $convertToWebp;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        
        // Load settings from database
        $this->loadSettings();
    }

    /**
     * Load media settings from database
     */
    protected function loadSettings(): void
    {
        $this->generateThumbnails = Setting::get('media_generate_thumbnails', 'true') === 'true';
        $this->convertToWebp = Setting::get('media_convert_to_webp', 'true') === 'true';
        $this->webpQuality = (int) Setting::get('media_webp_quality', '85');
        
        // Build sizes array from settings
        $this->sizes = [
            'thumb' => [
                (int) Setting::get('media_thumb_width', '300'),
                (int) Setting::get('media_thumb_height', '300')
            ],
            'medium' => [
                (int) Setting::get('media_medium_width', '600'),
                (int) Setting::get('media_medium_height', '600')
            ],
            'large' => [
                (int) Setting::get('media_large_width', '1200'),
                (int) Setting::get('media_large_height', '1200')
            ],
        ];
    }

    /**
     * Upload file and generate WebP thumbnails with compression (if enabled)
     * 
     * @param UploadedFile $file
     * @param string|null $folder
     * @return array
     */
    public function upload(UploadedFile $file, ?string $folder = null): array
    {
        $uuid = (string) Str::uuid();
        
        // Determine extension based on settings
        $shouldConvertToWebp = $this->isImage($file) && $this->convertToWebp;
        $extension = $shouldConvertToWebp ? 'webp' : $file->getClientOriginalExtension();
        $filename = "{$uuid}.{$extension}";
        
        // Build folder path
        $folderPath = $folder ? "uploads/{$folder}" : 'uploads';
        
        $originalPath = "{$folderPath}/original/{$filename}";
        $generatedSizes = [];

        // Process images
        if ($this->isImage($file)) {
            try {
                $image = $this->manager->read($file->getRealPath());
                
                // Save original (with or without WebP conversion)
                if ($shouldConvertToWebp) {
                    $originalEncoded = $image->encode(new WebpEncoder(quality: $this->webpQuality));
                    Storage::disk('public')->put($originalPath, $originalEncoded);
                } else {
                    Storage::disk('public')->put($originalPath, file_get_contents($file->getRealPath()));
                }
                
                // Generate thumbnails if enabled
                if ($this->generateThumbnails) {
                    foreach ($this->sizes as $sizeName => [$width, $height]) {
                        $sizePath = "{$folderPath}/{$sizeName}/{$filename}";
                        
                        // Resize and save
                        $resized = $image->cover($width, $height);
                        
                        if ($shouldConvertToWebp) {
                            $encoded = $resized->encode(new WebpEncoder(quality: $this->webpQuality));
                            Storage::disk('public')->put($sizePath, $encoded);
                        } else {
                            Storage::disk('public')->put($sizePath, $resized->encode());
                        }
                        
                        $generatedSizes[$sizeName] = $sizePath;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to process image: " . $e->getMessage());
            }
        } else {
            // Non-image files: store original without conversion
            Storage::disk('public')->put($originalPath, file_get_contents($file->getRealPath()));
        }

        return [
            'path' => $originalPath,
            'sizes' => $generatedSizes,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $shouldConvertToWebp ? 'image/webp' : $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => $this->getImageWidth($file),
            'height' => $this->getImageHeight($file),
        ];
    }

    /**
     * Check if file is an image
     */
    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    /**
     * Get image width
     */
    protected function getImageWidth(UploadedFile $file): ?int
    {
        if (!$this->isImage($file)) {
            return null;
        }

        try {
            $image = $this->manager->read($file->getRealPath());
            return $image->width();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get image height
     */
    protected function getImageHeight(UploadedFile $file): ?int
    {
        if (!$this->isImage($file)) {
            return null;
        }

        try {
            $image = $this->manager->read($file->getRealPath());
            return $image->height();
        } catch (\Exception $e) {
            return null;
        }
    }
}
