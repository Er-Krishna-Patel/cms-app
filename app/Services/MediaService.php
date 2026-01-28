<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Media Service - Enterprise Media Management with WordPress-Style Thumbnails
 * 
 * PRINCIPLE: Models are dumb, services are smart
 * 
 * This service layer handles:
 * ✓ File upload & storage operations
 * ✓ Thumbnail generation (300×300, 600×600, 1200×1200)
 * ✓ Business logic and validations
 * ✓ Multi-user file safety
 * ✓ Directory traversal protection
 * 
 * Media model remains pure data layer:
 * ✓ Relationships & scopes
 * ✓ Accessors & mutators  
 * ✓ Model events (auto file deletion on record delete)
 * ✓ No business logic
 */
class MediaService
{
    /**
     * Upload file and create media record with thumbnails
     * 
     * Handles: validation, file storage, thumbnail generation, metadata detection
     * 
     * @param UploadedFile $file
     * @param string|null $folder Target folder
     * @param int|null $userId User ID (defaults to auth()->id())
     * @param string $visibility 'public' or 'private'
     * @return Media|null Created media or null on failure
     */
    public static function uploadFile(
        UploadedFile $file, 
        ?string $folder = null, 
        ?int $userId = null,
        string $visibility = 'public'
    ): ?Media {
        try {
            $userId = $userId ?? auth()->id();
            if (!$userId) {
                Log::error('MediaService: No user ID provided for upload');
                return null;
            }

            // Validate file
            if (!$file->isValid()) {
                Log::error('MediaService: Invalid file uploaded');
                return null;
            }

            // Sanitize folder to prevent directory traversal
            if ($folder) {
                $folder = self::sanitizeFolder($folder);
            }

            // Use MediaUploader for images to generate thumbnails
            $uploader = new MediaUploader();
            $uploadData = $uploader->upload($file, $folder);

            // Create media record
            return Media::create([
                'user_id' => $userId,
                'filename' => $uploadData['filename'],
                'original_name' => $uploadData['original_name'],
                'mime_type' => $uploadData['mime_type'],
                'size' => $uploadData['size'],
                'width' => $uploadData['width'],
                'height' => $uploadData['height'],
                'disk' => 'public',
                'path' => $uploadData['path'],
                'sizes' => $uploadData['sizes'], // WordPress-style thumbnails
                'folder' => $folder,
            ]);

        } catch (\Exception $e) {
            Log::error('MediaService: Upload failed - ' . $e->getMessage());
            return null;
        }
    }
                'size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'disk' => 'public',
                'path' => $path,
                'folder' => $folder,
                'visibility' => $visibility,
            ]);
        } catch (\Exception $e) {
            Log::error('MediaService: Upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Move media to different folder
     * 
     * @param Media $media
     * @param string $folder Target folder
     * @return bool Success status
     */
    public static function moveToFolder(Media $media, string $folder): bool
    {
        try {
            // Sanitize folder
            $folder = self::sanitizeFolder($folder);

            $oldPath = $media->path;
            $filename = basename($oldPath);
            
            // Construct new path with user_id for multi-user safety
            $userId = $media->user_id;
            $newPath = "uploads/{$userId}/{$folder}/{$filename}";

            $disk = Storage::disk($media->disk);
            
            // Copy to new location
            $disk->copy($oldPath, $newPath);

            // Delete old file (physical deletion only)
            $disk->delete($oldPath);

            // Update record
            $media->update([
                'path' => $newPath,
                'folder' => $folder,
            ]);

            Log::info("MediaService: Moved media {$media->id} to folder: {$folder}");
            return true;
        } catch (\Exception $e) {
            Log::error('MediaService: Move failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Copy/Duplicate media file
     * 
     * Creates a new media record with duplicated file
     * 
     * @param Media $media
     * @param string|null $name Optional custom name for copy
     * @return Media|null
     */
    public static function copy(Media $media, ?string $name = null): ?Media
    {
        try {
            $disk = Storage::disk($media->disk);
            $newFilename = Str::uuid() . '.' . $media->extension;
            $newPath = dirname($media->path) . '/' . $newFilename;

            // Copy file
            $disk->copy($media->path, $newPath);

            // Create new media record
            $copy = Media::create([
                'user_id' => $media->user_id,
                'filename' => $newFilename,
                'original_name' => $name ?? $media->original_name . ' (Copy)',
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'width' => $media->width,
                'height' => $media->height,
                'disk' => $media->disk,
                'path' => $newPath,
                'alt_text' => $media->alt_text,
                'caption' => $media->caption,
                'description' => $media->description,
                'folder' => $media->folder,
                'visibility' => $media->visibility ?? 'public',
            ]);

            Log::info("MediaService: Copied media {$media->id} to {$copy->id}");
            return $copy;
        } catch (\Exception $e) {
            Log::error('MediaService: Copy failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sanitize folder name to prevent directory traversal attacks
     * 
     * Security measure: Blocks attempts like "../../../etc/passwd"
     * 
     * @param string $folder
     * @return string Safe folder name
     */
    private static function sanitizeFolder(string $folder): string
    {
        // Remove path traversal attempts
        $folder = str_replace('..', '', $folder);
        $folder = str_replace('/', '-', $folder);
        $folder = str_replace('\\', '-', $folder);
        
        // Remove special characters, keep only alphanumeric, hyphens, underscores
        $folder = preg_replace('/[^a-zA-Z0-9\-_]/', '', $folder);
        
        return trim($folder);
    }
}
