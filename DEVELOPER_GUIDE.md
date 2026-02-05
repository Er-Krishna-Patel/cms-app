# 🎨 Medicotrick CMS - Developer Visual Guide

![Laravel](https://img.shields.io/badge/Laravel-12.48.1-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4.15-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC34A?style=for-the-badge&logo=alpine.js&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

## 🚀 Quick Start (5 Minutes)

```bash
# 1️⃣ Clone & Enter
git clone https://github.com/Er-Krishna-Patel/cms-app.git
cd cms-app

# 2️⃣ Database Setup
mysql -u root -e "CREATE DATABASE cms;"
mysql -u root cms < database_export.sql

# 3️⃣ Environment
cp .env.example .env
php artisan key:generate

# 4️⃣ Dependencies
composer install
npm install

# 5️⃣ Build & Run
npm run build
php artisan serve
```

**🌐 Open**: http://127.0.0.1:8000

---

## 📱 UI Overview & Features

### 🔐 Admin Dashboard
```
┌─────────────────────────────────────────┐
│ 🏠 CustomCMS                    🔔 👤   │
├─────────────────────────────────────────┤
│ 📊 Dashboard                            │
│ 📝 Posts       📁 Categories   🏷️ Tags  │
│ 📷 Media       👥 Users        ⚙️ Settings│
│ 📋 Activity    🚪 Logout               │
└─────────────────────────────────────────┘
```

### 📷 Media Library (WordPress-style)
```
┌─────────────────────┬───────────────────┐
│ 🔍 Search Media     │ 📋 Attachment     │
│ 📁 All Types ▼      │    Details        │
│ ⊞ Grid  ≡ List     │                   │
├─────────────────────┤ 🖼️ Preview        │
│ [🖼️] [🖼️] [🖼️]      │ 📝 Alt text      │
│ [🖼️] [🖼️] [🖼️]      │ 📝 Caption       │
│ [🖼️] [🖼️] [🖼️]      │ 📝 Description   │
│                     │ 📊 Metadata      │
│ 📤 Upload Files     │ 🔗 Actions       │
└─────────────────────┴───────────────────┘
```

---

## 🏗️ Project Architecture

### 📁 Frontend Structure
```
resources/
├── 🎨 css/
│   ├── app.css              # Main styles + Tailwind
│   ├── media-modal.css      # Media popup styles
│   └── post-editor.css      # Editor customization
├── 🚀 js/
│   ├── app.js               # Main entry point
│   ├── media-upload.js      # 📤 File upload handler
│   ├── post-editor.js       # ✏️ TinyMCE integration
│   ├── modules/
│   │   ├── media-manager.ts # 📁 Media operations
│   │   └── media-modal.ts   # 🖼️ Modal functionality
│   └── utils/
│       ├── confirmation.ts  # ⚠️ Delete confirmations
│       └── form-submission.ts # 📝 Form handlers
└── 🖥️ views/
    ├── admin/               # 👑 Admin pages
    ├── auth/                # 🔐 Login/register
    ├── components/          # 🧩 Reusable UI parts
    └── layouts/             # 📐 Page templates
```

### 🔧 Backend Structure
```
app/
├── 🎮 Http/Controllers/
│   ├── Admin/
│   │   ├── MediaController.php      # 📷 File uploads
│   │   ├── PostController.php       # 📝 Content CRUD
│   │   ├── SettingsController.php   # ⚙️ Configuration
│   │   └── UserController.php       # 👥 User management
│   └── Api/
│       └── PostApiController.php    # 🌐 REST endpoints
├── 📊 Models/
│   ├── Media.php           # 📷 File metadata + thumbnails
│   ├── Post.php            # 📝 Content with tags/categories
│   ├── Setting.php         # ⚙️ App configuration
│   └── ActivityLog.php     # 📋 User action tracking
├── 🔧 Services/
│   ├── MediaService.php    # 📁 File operations
│   └── MediaUploader.php   # 🖼️ Image processing + WebP
└── 🛡️ Middleware/
    └── RoleMiddleware.php  # 🔒 Permission control
```

---

## 🗄️ Database Schema

### 📷 Media Table (WordPress-style)
```sql
media
├── 🆔 id
├── 📝 original_name        # "photo.jpg"
├── 📝 file_name           # "1642512345_photo.jpg"
├── 📝 file_path           # "storage/media/2024/01/"
├── 📊 mime_type           # "image/jpeg"
├── 📏 file_size           # 2048000 (bytes)
├── 📐 width, height       # 1920, 1080
├── 🏷️ alt_text, caption, description
├── 🗂️ sizes               # JSON: thumbnail URLs
└── 🕒 created_at, updated_at
```

### 📝 Posts Table
```sql
posts
├── 🆔 id
├── 📝 title, slug, content
├── 📊 status              # "draft", "published"
├── 🕒 published_at        # Scheduling
├── 👤 user_id (author)
├── 🖼️ featured_image_id
└── 📁 categories, tags (relationships)
```

---

## 🎯 Key Features Implementation

### 1️⃣ WordPress-Style Media Upload

**📁 File**: `resources/js/media-upload.js`
```javascript
// ✨ Features:
- 📤 Multi-file drag & drop
- 📊 Real-time progress bars
- 🖼️ Instant thumbnails (300×300)
- 🔍 Live search & filtering
- 📱 Responsive grid/list view
```

**🖼️ Thumbnail Generation**: `app/Services/MediaUploader.php`
```php
// 🎨 Auto-generates:
- thumb: 300×300px    (Grid display)
- medium: 600×600px   (Sidebar preview)  
- large: 1200×1200px  (Full view)
- 🌐 WebP conversion (85% quality)
```

### 2️⃣ Real-time Settings

**⚙️ Location**: `/admin/settings` → Media Settings
```
┌─────────────────────────────────────┐
│ 📷 Media Settings                   │
│                                     │
│ ☑️ Generate Thumbnails              │
│ ☑️ Convert to WebP                  │
│ 🎚️ WebP Quality: [85]    %         │
│                                     │
│ 🖼️ Thumbnail: [300] × [300] px     │
│ 🖼️ Medium:    [600] × [600] px     │
│ 🖼️ Large:     [1200] × [1200] px   │
│                                     │
│ 📊 Max Upload: [10240] KB           │
│                                     │
│        💾 Save Settings             │
└─────────────────────────────────────┘
```

### 3️⃣ Alpine.js Integration

**📁 File**: `resources/views/admin/media/index.blade.php`
```javascript
// 🚀 Reactive Data:
x-data="{
  viewMode: 'grid',           // 🔄 Grid/List toggle
  searchTerm: '',             // 🔍 Live search
  selectedMedia: null,        // 👆 Click selection
  uploadingFiles: [],         // 📤 Progress tracking
  filteredMedia: computed()   // 🎯 Auto-filtering
}"
```

---

## 🛠️ Development Workflow

### 🌿 Branch Strategy
```
main (🔒 Protected)
├── developer (👥 Team work)
    ├── feature/media-filters
    ├── feature/bulk-actions  
    └── feature/image-editor
```

### 🔄 Making Changes
```bash
# 1️⃣ Switch to developer
git checkout developer

# 2️⃣ Create feature branch
git checkout -b feature/your-feature

# 3️⃣ Code & test
npm run dev    # 👁️ Watch for changes

# 4️⃣ Commit & push
git add .
git commit -m "✨ Add new feature"
git push origin feature/your-feature

# 5️⃣ Create PR to 'developer'
```

### 🚦 Testing
```bash
# 🧪 Backend tests
php artisan test

# 🎨 Frontend build
npm run build

# 🔍 Code quality
./vendor/bin/phpstan analyse
```

---

## 📋 Common Development Tasks

### 🆕 Adding New Media Features

1. **Backend**: Extend `MediaController.php`
2. **Frontend**: Modify `media-upload.js`
3. **UI**: Update `media/index.blade.php`
4. **Styles**: Add to `media-modal.css`

### 📝 Adding Post Features

1. **Model**: Extend `Post.php` relationships
2. **Migration**: Add database columns
3. **Controller**: Update `PostController.php`
4. **Views**: Modify `posts/` templates

### ⚙️ Adding Settings

1. **Migration**: Add to `settings` table
2. **Seeder**: Update `MediaSettingsSeeder.php`
3. **Form**: Add to `settings/index.blade.php`
4. **Validation**: Update `SettingsController.php`

---

## 🎨 UI Components Library

### 🧩 Blade Components
```
components/
├── ui/
│   ├── Button.blade.php      # 🔲 Styled buttons
│   ├── Input.blade.php       # 📝 Form inputs
│   ├── Modal.blade.php       # 🖼️ Popup dialogs
│   └── Select.blade.php      # 📋 Dropdowns
├── editor.blade.php          # ✏️ TinyMCE wrapper
├── media-library.blade.php   # 📁 File browser
└── delete-form.blade.php     # 🗑️ Confirmation
```

### 🎨 Tailwind Classes
```css
/* 🎯 Common Patterns */
.btn-primary    → bg-blue-600 text-white px-4 py-2 rounded
.card          → bg-white rounded-lg shadow border
.input         → border border-gray-300 rounded px-3 py-2
.grid-media    → grid grid-cols-5 gap-4 aspect-square
```

---

## 🔧 Configuration

### ⚙️ Key Settings Files
```
config/
├── database.php      # 🗄️ DB connections
├── filesystems.php   # 📁 Storage config
├── editor.php        # ✏️ TinyMCE options
└── app.php          # 🏠 General settings
```

### 🌍 Environment Variables
```bash
# 🗄️ Database
DB_DATABASE=cms
DB_USERNAME=root
DB_PASSWORD=

# 📁 Storage
FILESYSTEM_DISK=local

# ✏️ Editor
EDITOR_HEIGHT=400
EDITOR_PLUGINS=image,link,lists

# 🖼️ Media (via settings table)
MEDIA_THUMBNAILS=true
MEDIA_WEBP_QUALITY=85
```

---

## 🐛 Troubleshooting

### ❗ Common Issues

| 🔴 Issue | ✅ Solution |
|----------|-------------|
| Upload not working | Check `npm run dev` is running |
| Images not showing | Run `php artisan storage:link` |
| JS errors | Check browser console, rebuild with `npm run build` |
| MySQL connection | Ensure XAMPP MySQL is running |
| Permission denied | `chmod -R 755 storage/ bootstrap/cache/` |

### 🔍 Debug Tools
```bash
# 📋 Laravel logs
tail -f storage/logs/laravel.log

# 🌐 JS console errors
F12 → Console tab

# 🗄️ Database queries  
# Add to .env: DB_LOG=true
```

---

## 📞 Support & Resources

### 🔗 Useful Links
- **📚 Laravel Docs**: https://laravel.com/docs/12.x
- **🎨 Tailwind CSS**: https://tailwindcss.com/docs
- **⛰️ Alpine.js**: https://alpinejs.dev/start-here
- **✏️ TinyMCE**: https://www.tiny.cloud/docs/

### 📧 Team Communication
- **🐛 Issues**: GitHub Issues tab
- **💡 Features**: GitHub Discussions  
- **🔄 Pull Requests**: Review required for `main`

### 📁 Important Files for New Developers
1. `database_export.sql` - 🗄️ Import this first
2. `DATABASE_SETUP.md` - 📋 Setup instructions
3. `CONTRIBUTING.md` - 🤝 Development guide
4. `.env.example` - ⚙️ Configuration template

---

**🎉 Happy Coding! Start with the Quick Start guide above! 🚀**

---