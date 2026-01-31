# Media Management - Quick Start Guide

## 🎯 What's New

Your media management system now has:
- ✅ **Bulk Select/Delete** - Select multiple media items
- ✅ **Media Selector Modal** - Use anywhere on your site
- ✅ **SEO Optimization** - Auto alt text, captions, schema.org
- ✅ **Image Compression** - WebP conversion, 3 thumbnail sizes
- ✅ **Inline Upload** - Upload from selector without leaving page

---

## 🚀 Quick Usage

### Option 1: Use Media Selector in Any Page

```blade
<!-- Your template file -->
<button onclick="openMediaSelector(function(selected) {
    console.log('Selected:', selected);
    // selected[0].url - Full image URL
    // selected[0].thumb_url - Thumbnail
    // selected[0].id - Media ID
})">
    Pick Image
</button>

<!-- Add once in your layout -->
@include('components.media-selector-modal', ['media' => Media::all()])
```

### Option 2: Admin Media Library

1. Go to: `/admin/media`
2. Upload files
3. Select items (checkboxes)
4. Bulk delete or view details
5. Edit SEO fields (alt, caption, description)

### Option 3: For Blog/Post Editor

```blade
<label>Featured Image</label>
<button class="btn btn-primary" onclick="selectFeaturedImage()">
    Browse
</button>

<img id="featured-preview" style="max-width: 300px;">
<input type="hidden" name="featured_image_id">

<script>
function selectFeaturedImage() {
    openMediaSelector(function(selected) {
        document.getElementById('featured-preview').src = selected[0].medium_url;
        document.querySelector('[name=featured_image_id]').value = selected[0].id;
    });
}
</script>

@include('components.media-selector-modal', ['media' => Media::all()])
```

---

## 📁 File Structure

```
resources/views/
├── admin/media/index.blade.php          ← Media library UI with bulk select
└── components/
    ├── media-selector-modal.blade.php   ← Reusable selector (NEW)
    └── media-selector-helper.blade.php  ← Documentation

app/
├── Http/Controllers/Admin/MediaController.php  ← Added bulk delete
├── Services/MediaService.php            ← Upload handling
├── Helpers/MediaSeoHelper.php          ← SEO optimization (NEW)
└── Models/Media.php                     ← Media model

routes/web.php                           ← Route for bulk delete (already added)

MEDIA_MANAGEMENT.md                      ← Full documentation (NEW)
```

---

## 🔧 Configuration

### In `.env`
```
MEDIA_GENERATE_THUMBNAILS=true
MEDIA_CONVERT_TO_WEBP=true
MEDIA_WEBP_QUALITY=85
MEDIA_THUMB_WIDTH=300
MEDIA_THUMB_HEIGHT=300
MEDIA_MEDIUM_WIDTH=600
MEDIA_MEDIUM_HEIGHT=600
MEDIA_LARGE_WIDTH=1200
MEDIA_LARGE_HEIGHT=1200
```

---

## 🎨 UI Features

### Media Library (`/admin/media`)
- 📤 Upload button
- 🔍 Search by filename
- 🏷️ Filter by type
- ⊞/≡ Grid/List toggle
- ☑️ Bulk select checkboxes (NEW)
- 🗑️ Bulk delete button (NEW)
- 👁️ Side panel with details
- ✏️ Edit alt text, caption, description

### Media Selector Modal
- Same search, filter, view options
- 📤 Upload new files inline
- ☑️ Multi-select support
- 👁️ Preview panel
- ✅ Select button with count

---

## 🛡️ Security

✅ User-owned media (see own files)  
✅ Admin can view all  
✅ CSRF token protection  
✅ Role-based access  
✅ File validation  
✅ Directory traversal protection  

---

## 📊 SEO Features

### Auto-Generated
- Alt text from filename
- Slugified names
- Filename validation
- Schema.org JSON-LD markup
- Meta tags (og:image, etc.)

### Manual Entry
- Alt text
- Caption
- Description
- Custom slug

### Example
```php
use App\Helpers\MediaSeoHelper;

$meta = MediaSeoHelper::generateMetadata('sunset-beach.jpg');
// Returns: alt_text, title, caption, slug, validation

$schema = MediaSeoHelper::getSchemaOrg($imageData);
// Returns JSON-LD for search engines
```

---

## 🔄 API Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/admin/media` | Upload file(s) |
| GET | `/admin/media` | List media |
| DELETE | `/admin/media/{id}` | Delete single |
| POST | `/admin/media/bulk-delete` | Delete multiple (NEW) |
| POST | `/admin/tinymce/upload` | TinyMCE upload |
| POST | `/admin/ckeditor/upload` | CKEditor upload |

---

## 💾 Storage Structure

```
storage/app/public/uploads/
├── original/{uuid}.webp      ← Full size
├── thumb/{uuid}.webp         ← 300×300
├── medium/{uuid}.webp        ← 600×600
└── large/{uuid}.webp         ← 1200×1200
```

All images auto-compressed and converted to WebP for better performance.

---

## 🐛 Troubleshooting

### Modal doesn't open
```javascript
// Make sure it's included in layout
@include('components.media-selector-modal', ['media' => Media::all()])

// Call it correctly
openMediaSelector(function(selected) { ... });
```

### Images not showing
```bash
# Create storage symlink
php artisan storage:link

# Clear cache
php artisan config:cache
php artisan cache:clear

# Rebuild assets
npm run build
```

### Bulk delete not working
- Check server logs: `storage/logs/laravel.log`
- Verify CSRF token in request
- Check user permissions (admin role required)
- Ensure file permissions on storage directory

---

## 📝 Examples

### Set featured image in admin
```blade
<button onclick="openMediaSelector(function(s) {
    document.querySelector('[name=featured_id]').value = s[0].id;
    document.getElementById('preview').src = s[0].medium_url;
})">Choose Image</button>

<input type="hidden" name="featured_id">
<img id="preview">

@include('components.media-selector-modal', ['media' => Media::all()])
```

### Gallery selector (multiple)
```blade
<button onclick="openMediaSelector(function(selected) {
    const gallery = selected.map(s => `
        <div class="gallery-item" data-id="${s.id}">
            <img src="${s.thumb_url}" alt="${s.alt_text}">
        </div>
    `).join('');
    document.getElementById('gallery').innerHTML = gallery;
})">
    Add to Gallery
</button>

<div id="gallery"></div>

@include('components.media-selector-modal', ['media' => Media::all()])
```

### Hero image with SEO
```blade
<div class="hero">
    <button class="btn-select" onclick="openMediaSelector(heroSelected)">
        Select Hero Image
    </button>
    <img id="hero-img" alt="Hero Image">
</div>

<script>
function heroSelected(selected) {
    const media = selected[0];
    document.getElementById('hero-img').src = media.large_url;
    document.getElementById('hero-img').alt = media.alt_text;
    
    // Store for form submission
    document.querySelector('[name=hero_image_id]').value = media.id;
}
</script>

@include('components.media-selector-modal', ['media' => Media::all()])
```

---

## 📚 Full Documentation

For complete details, see: [MEDIA_MANAGEMENT.md](MEDIA_MANAGEMENT.md)

---

## 🎁 What's Included

✅ Media Library UI with bulk operations  
✅ Reusable media selector modal  
✅ Image compression & WebP conversion  
✅ SEO optimization helper  
✅ Bulk delete API endpoint  
✅ Complete documentation  
✅ Working examples  

---

**Ready to use!** Start with the Quick Usage examples above. 🚀
