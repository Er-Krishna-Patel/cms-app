<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class MediaSettingsSeeder extends Seeder
{
    /**
     * Run the media settings seeder.
     */
    public function run(): void
    {
        $settings = [
            // Image Processing
            [
                'key' => 'media_generate_thumbnails',
                'value' => 'true',
                'group' => 'media',
                'type' => 'boolean',
                'description' => 'Automatically generate thumbnails when uploading images'
            ],
            [
                'key' => 'media_convert_to_webp',
                'value' => 'true',
                'group' => 'media',
                'type' => 'boolean',
                'description' => 'Convert images to WebP format for better compression'
            ],
            [
                'key' => 'media_webp_quality',
                'value' => '85',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'WebP compression quality (1-100, recommended: 85)'
            ],
            
            // Thumbnail Sizes
            [
                'key' => 'media_thumb_width',
                'value' => '300',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'Thumbnail width in pixels (for grid view)'
            ],
            [
                'key' => 'media_thumb_height',
                'value' => '300',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'Thumbnail height in pixels (for grid view)'
            ],
            [
                'key' => 'media_medium_width',
                'value' => '600',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'Medium image width in pixels (for previews)'
            ],
            [
                'key' => 'media_medium_height',
                'value' => '600',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'Medium image height in pixels (for previews)'
            ],
            [
                'key' => 'media_large_width',
                'value' => '1200',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'Large image width in pixels (for frontend display)'
            ],
            [
                'key' => 'media_large_height',
                'value' => '1200',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'Large image height in pixels (for frontend display)'
            ],
            
            // Upload Limits
            [
                'key' => 'media_max_upload_size',
                'value' => '10240',
                'group' => 'media',
                'type' => 'integer',
                'description' => 'Maximum upload file size in KB (default: 10MB)'
            ],
            [
                'key' => 'media_allowed_types',
                'value' => json_encode(['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'mp4', 'mp3']),
                'group' => 'media',
                'type' => 'json',
                'description' => 'Allowed file types for upload'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
