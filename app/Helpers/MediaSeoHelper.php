<?php

namespace App\Helpers;

use Illuminate\Support\Str;

/**
 * Media SEO Helper
 * Optimizes media files for search engines
 */
class MediaSeoHelper
{
    /**
     * Generate SEO-friendly alt text from filename
     */
    public static function generateAltText(string $filename): string
    {
        // Remove extension
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Replace common separators with spaces
        $name = preg_replace('/[-_.]/', ' ', $name);
        
        // Convert to title case
        return Str::title($name);
    }

    /**
     * Generate SEO-friendly slug from filename
     */
    public static function generateSlug(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        return Str::slug($name);
    }

    /**
     * Validate filename for SEO
     * - Should not be too long
     * - Should contain meaningful keywords
     * - Should not have too many special characters
     */
    public static function validateFilename(string $filename): array
    {
        $errors = [];
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Check length
        if (strlen($name) < 3) {
            $errors[] = 'Filename too short. Use at least 3 characters.';
        }
        if (strlen($name) > 100) {
            $errors[] = 'Filename too long. Use maximum 100 characters.';
        }

        // Check for too many numbers only
        if (preg_match('/^\d+$/', $name)) {
            $errors[] = 'Filename should contain descriptive text, not just numbers.';
        }

        // Check for special characters
        if (preg_match('/[^\w\s-]/', $name)) {
            $errors[] = 'Filename contains invalid special characters.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'suggestions' => [
                'alt_text' => self::generateAltText($filename),
                'slug' => self::generateSlug($filename)
            ]
        ];
    }

    /**
     * Generate SEO metadata for image
     */
    public static function generateMetadata(string $filename, ?string $context = null): array
    {
        $validation = self::validateFilename($filename);
        
        return [
            'alt_text' => $validation['suggestions']['alt_text'],
            'title' => Str::title(pathinfo($filename, PATHINFO_FILENAME)),
            'caption' => $context ? "Image: {$context}" : null,
            'slug' => $validation['suggestions']['slug'],
            'filename' => $filename,
            'validation' => $validation
        ];
    }

    /**
     * Get image meta tags for SEO
     */
    public static function getMetaTags(array $imageData): string
    {
        $alt = htmlspecialchars($imageData['alt_text'] ?? 'Image');
        $title = htmlspecialchars($imageData['title'] ?? '');
        
        return <<<HTML
        <!-- Image SEO Meta Tags -->
        <meta property="og:image" content="{$imageData['url']}">
        <meta property="og:image:type" content="{$imageData['mime_type']}">
        <meta property="og:image:alt" content="$alt">
        <meta name="image" content="{$imageData['url']}">
        <meta name="description" content="$alt">
        HTML;
    }

    /**
     * Generate schema.org ImageObject
     */
    public static function getSchemaOrg(array $imageData): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ImageObject',
            'name' => $imageData['alt_text'] ?? 'Image',
            'url' => $imageData['url'],
            'description' => $imageData['caption'] ?? $imageData['alt_text'],
            'datePublished' => $imageData['created_at'] ?? now()->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => 'CMS Admin'
            ]
        ];

        if (isset($imageData['width']) && isset($imageData['height'])) {
            $schema['width'] = $imageData['width'];
            $schema['height'] = $imageData['height'];
        }

        return json_encode($schema);
    }
}
