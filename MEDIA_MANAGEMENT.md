# Media Management System - Complete Documentation

## Overview
This is an enterprise-grade media management system for Laravel CMS with advanced features like image compression, WebP conversion, SEO optimization, and site-wide media selector.

## Features

### ✅ Already Implemented
- **Upload UI** - Modern, responsive media upload interface
- **Grid & List Views** - Switch between grid and list layouts
- **Media Filtering** - Filter by type (images, videos, documents)
- **Search** - Full-text search on filenames
- **Image Compression** - Automatic compression with configurable quality
- **WebP Conversion** - Auto-convert to WebP format (with fallbacks)
- **Responsive Thumbnails** - 3 sizes: thumb (300×300), medium (600×600), large (1200×1200)
- **Bulk Select & Delete** - Select multiple items, bulk delete operations
- **SEO Fields** - Alt text, caption, description for each media
- **Activity Logging** - Track all media operations

### 🆕 New Features Added
- **Media Selector Modal** - Reusable component for selecting media anywhere
- **Inline Upload in Modal** - Upload directly from media selector
- **Bulk Operations** - Bulk delete, select all, clear selection
- **SEO Helper Class** - Generate SEO-friendly filenames and metadata
- **Schema.org Support** - Automatic JSON-LD generation for images

---

## Usage Guide

### 1. **Using Media Selector in Your Templates**

#### Simple Usage - Any Page/Template
```blade
<!-- Add button to open media selector -->
<button onclick="openMediaSelector(handleMediaSelection)" class="btn btn-primary">
    Select Image
</button>

<!-- Include modal in your layout -->
@include('components.media-selector-modal', ['media' => Media::all()])

<!-- Handle selection -->
<script>
function handleMediaSelection(selected) {
    console.log('Selected media:', selected);
    // selected is array of media objects
    // Each has: id, name, url, thumb_url, medium_url, large_url, alt_text, etc.
    
    // Example: Set image src
    document.getElementById('featured-image').src = selected[0].url;
    
    // Example: Get multiple selections
    selected.forEach(media => {
        console.log(media.id, media.url);
    });
}
</script>
```

#### In Admin Pages
```blade
@extends('layouts.admin')

@section('content')
<div>
    <h1>Featured Image</h1>
    
    <button onclick="openMediaSelector(function(selected) {
        document.getElementById('featured-img').src = selected[0].thumb_url;
        document.getElementById('featured-id').value = selected[0].id;
    })">
        Browse Media
    </button>
    
    <img id="featured-img" src="" alt="Preview">
    <input type="hidden" id="featured-id" name="featured_image_id">
</div>

@include('components.media-selector-modal', ['media' => Media::all()])
@endsection
```

#### In Blog Post Editor
```blade
<!-- In post editor form -->
<label>Featured Image</label>
<button onclick="openMediaSelector(function(selected) {
    document.querySelector('[name=featured_image_id]').value = selected[0].id;
    document.getElementById('featured-preview').innerHTML = 
        `<img src="${selected[0].thumb_url}" alt="${selected[0].alt_text}">`;
})">
    Choose Image
</button>
<div id="featured-preview"></div>
```

---

### 2. **Media Library - Admin Panel**

#### Access
- Go to: `/admin/media`
- Only for authenticated admins/editors

#### Upload Files
1. Click **"Upload Files"** button
2. Select multiple images/videos/documents
3. Progress bar shows upload status
4. Files auto-compress and convert to WebP (if image)

#### Organize Media
- **Search** - Find media by filename
- **Filter** - By type (Images, Videos, PDFs)
- **View** - Toggle between Grid/List view
- **Select** - Click checkbox or item to select
- **Bulk Delete** - Select items → Click Delete

#### Edit Media Details
1. Click any item to select
2. Right sidebar shows details
3. Edit fields:
   - Alt Text (for SEO)
   - Caption
   - Description
4. Click **"Copy URL"** to get media link
5. Click **"Open File"** to view
6. Click **"Delete"** to remove

---

### 3. **SEO Optimization**

#### Auto-Generated SEO
The system automatically:
- Generates alt text from filename
- Creates slugs for media
- Validates filenames for SEO
- Generates schema.org metadata

#### Using SEO Helper

```php
use App\Helpers\MediaSeoHelper;

// Generate metadata from filename
$metadata = MediaSeoHelper::generateMetadata('my-cat-photo.jpg', 'Home Page Hero');

// Validate filename
$validation = MediaSeoHelper::validateFilename('my-image.jpg');
if ($validation['valid']) {
    echo "Good filename!";
} else {
    echo "Issues: " . implode(', ', $validation['errors']);
}

// Get meta tags for image
$metaTags = MediaSeoHelper::getMetaTags($mediaData);

// Get schema.org JSON-LD
$schema = MediaSeoHelper::getSchemaOrg($mediaData);
echo "<script type='application/ld+json'>$schema</script>";
```

#### Best Practices
✅ **Good filenames:**
- `sunset-beach-california.jpg`
- `product-red-leather-shoes.jpg`
- `team-meeting-2026-january.jpg`

❌ **Bad filenames:**
- `IMG_1234.jpg`
- `photo-123456.jpg`
- `zzz.jpg`
- `image@#$%.jpg`

---

### 4. **Image Compression Settings**

Configured in `.env` or database settings:

```php
// Generate thumbnails
MEDIA_GENERATE_THUMBNAILS=true

// Convert to WebP
MEDIA_CONVERT_TO_WEBP=true

// WebP Quality (1-100)
MEDIA_WEBP_QUALITY=85

// Thumbnail sizes
MEDIA_THUMB_WIDTH=300
MEDIA_THUMB_HEIGHT=300
MEDIA_MEDIUM_WIDTH=600
MEDIA_MEDIUM_HEIGHT=600
MEDIA_LARGE_WIDTH=1200
MEDIA_LARGE_HEIGHT=1200
```

---

### 5. **API Endpoints**

#### Upload Single File
```
POST /admin/media
Content-Type: multipart/form-data

files[]: <file>
```

#### Upload Multiple Files
```
POST /admin/media
Content-Type: multipart/form-data

files[]: <file1>
files[]: <file2>
files[]: <file3>
```

#### Bulk Delete
```
POST /admin/media/bulk-delete
Content-Type: application/json
X-CSRF-TOKEN: <token>

{
  "ids": [1, 2, 3]
}
```

#### Get Media List
```
GET /admin/media
Query params:
- search=filename
- type=image|video|pdf
- folder=folder_name
```

---

### 6. **Database Fields**

#### Media Table
```php
id              - Unique ID
user_id         - Owner user
filename        - Generated UUID filename
original_name   - Original uploaded name
mime_type       - File MIME type
size            - File size in bytes
width           - Image width (null for non-images)
height          - Image height (null for non-images)
path            - Storage path
sizes           - JSON: thumbnail paths
folder          - Folder/category
alt_text        - SEO: Alt text
caption         - SEO: Caption
description     - SEO: Description
disk            - Storage disk (usually 'public')
visibility      - 'public' or 'private'
created_at      - Upload date
updated_at      - Last modified
```

---

### 7. **File Storage**

Files are organized in public storage:
```
storage/app/public/
├── uploads/
│   ├── original/
│   │   ├── {uuid}.webp
│   │   └── ...
│   ├── thumb/
│   │   ├── {uuid}.webp (300×300)
│   │   └── ...
│   ├── medium/
│   │   ├── {uuid}.webp (600×600)
│   │   └── ...
│   └── large/
│       ├── {uuid}.webp (1200×1200)
│       └── ...
```

---

### 8. **Security Features**

✅ **Built-in protections:**
- User ownership verification (users see own files only)
- Admin can see all files
- File type validation
- MIME type checking
- Directory traversal prevention
- CSRF token protection
- Role-based access control

---

### 9. **Performance**

✅ **Optimizations:**
- Images auto-compressed to WebP
- Responsive thumbnails (3 sizes)
- Lazy loading in grid view
- Pagination (20 items per page)
- Efficient database queries with indexes
- Browser caching for images

---

### 10. **Advanced: Custom Upload Handler**

```php
use App\Services\MediaService;

// Upload with custom folder
$media = MediaService::uploadFile(
    $request->file('image'),
    'blog-posts',
    auth()->id(),
    'public'
);

// Access media
echo $media->url;           // Full path
echo $media->thumb_url;     // Thumbnail
echo $media->medium_url;    // Medium size
echo $media->alt_text;      // SEO alt
```

---

### 11. **Troubleshooting**

#### Images not showing
- Check storage symlink: `php artisan storage:link`
- Clear config cache: `php artisan config:cache`
- Rebuild frontend: `npm run build`

#### Upload fails
- Check file permissions on `storage/` directory
- Verify upload size limit in `.env`
- Check server disk space

#### WebP not working
- Ensure GD extension is installed
- Update `intervention/image` package
- Check format support: `php artisan tinker` → `extension_loaded('gd')`

---

## Component Files

### Main Files
- **Media Library**: `resources/views/admin/media/index.blade.php`
- **Selector Modal**: `resources/views/components/media-selector-modal.blade.php`
- **Controller**: `app/Http/Controllers/Admin/MediaController.php`
- **Service**: `app/Services/MediaService.php`
- **SEO Helper**: `app/Helpers/MediaSeoHelper.php`

### Models
- **Media Model**: `app/Models/Media.php`

### Routes
- **Admin Routes**: `routes/web.php` (prefix: `/admin/media`)

---

## Quick Start

1. **Enable symlink** (if not done):
   ```bash
   php artisan storage:link
   ```

2. **Build assets**:
   ```bash
   npm run build
   ```

3. **Access media library**:
   - Go to: `http://yoursite.local/admin/media`

4. **Use in templates**:
   ```blade
   <button onclick="openMediaSelector(yourCallback)">Select Media</button>
   @include('components.media-selector-modal', ['media' => Media::all()])
   ```

---

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review SEO validation errors
3. Verify file permissions
4. Check browser console for JS errors

---

**Last Updated:** January 31, 2026  
**Version:** 1.0.0 - Complete Media Management System
