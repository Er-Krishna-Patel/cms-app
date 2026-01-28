<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'user_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'disk',
        'path',
        'sizes', // WordPress-style thumbnails
        'alt_text',
        'caption',
        'description',
        'folder',
        'visibility',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'sizes' => 'array', // JSON array of thumbnail paths
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'url',
        'preview_url',
        'thumb_url',
        'medium_url',
        'large_url',
        'is_image',
        'human_readable_size',
        'extension',
        'file_type',
    ];

    /**
     * Boot the model - Auto delete file when record is deleted
     */
    protected static function booted(): void
    {
        static::deleting(function (Media $media) {
            // Only delete file if not soft-deleted for recovery
            Storage::disk($media->disk)->delete($media->path);
        });

        static::forceDeleting(function (Media $media) {
            // Final deletion
            Storage::disk($media->disk)->delete($media->path);
        });
    }

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full URL to media file
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get preview URL (image only, null otherwise)
     * 
     * Frontend/Blade handles icon rendering, not the model
     */
    public function getPreviewUrlAttribute(): ?string
    {
        return $this->is_image ? $this->url : null;
    }

    /**
     * Get thumbnail URL (300×300) - WordPress-style
     * Uses generated thumbnail if available, falls back to original
     */
    public function getThumbUrlAttribute(): string
    {
        if ($this->is_image && isset($this->sizes['thumb'])) {
            return Storage::disk($this->disk)->url($this->sizes['thumb']);
        }
        return $this->url;
    }

    /**
     * Get medium URL (600×600) - WordPress-style
     */
    public function getMediumUrlAttribute(): string
    {
        if ($this->is_image && isset($this->sizes['medium'])) {
            return Storage::disk($this->disk)->url($this->sizes['medium']);
        }
        return $this->url;
    }

    /**
     * Get large URL (1200×1200) - WordPress-style
     */
    public function getLargeUrlAttribute(): string
    {
        if ($this->is_image && isset($this->sizes['large'])) {
            return Storage::disk($this->disk)->url($this->sizes['large']);
        }
        return $this->url;
    }

    /**
     * Check if media is an image
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get human-readable file size
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    /**
     * Get file type category (for frontend use only)
     */
    public function getFileTypeAttribute(): string
    {
        if ($this->is_image) return 'image';
        if (str_starts_with($this->mime_type, 'video/')) return 'video';
        if (str_starts_with($this->mime_type, 'audio/')) return 'audio';
        if (str_starts_with($this->mime_type, 'application/pdf')) return 'pdf';
        if (in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])) return 'document';
        if (in_array($this->mime_type, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])) return 'spreadsheet';
        return 'file';
    }

    /**
     * Scope: Get only images
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope: Get only videos
     */
    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    /**
     * Scope: Get only documents
     */
    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Scope: Get by folder
     */
    public function scopeByFolder($query, ?string $folder)
    {
        return $folder ? $query->where('folder', $folder) : $query;
    }

    /**
     * Scope: Get public media
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope: Get private media
     */
    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    /**
     * Scope: Search by filename or original name
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('filename', 'like', "%{$term}%")
                     ->orWhere('original_name', 'like', "%{$term}%");
    }

    /**
     * Scope: Recent first
     */
    public function scopeRecent($query)
    {
        return $query->latest('created_at');
    }

    /**
     * Scope: Get user's media
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get all available folders for current user
     */
    public static function getAvailableFolders(?int $userId = null): array
    {
        $userId = $userId ?? auth()->id();
        
        return self::where('user_id', $userId)
                   ->whereNotNull('folder')
                   ->distinct()
                   ->pluck('folder')
                   ->sort()
                   ->values()
                   ->toArray();
    }

    /**
     * Check if media is PDF
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}

