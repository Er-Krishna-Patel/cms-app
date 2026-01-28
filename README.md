# CustomCMS – Laravel-Based Content Management System

A modern, full-featured CMS built with Laravel 12, featuring user authentication, role-based access control, rich text editing, media uploads, and optional headless API.

## Quick Start

### Prerequisites

- PHP 8.4+
- Composer
- MySQL 10.4+ (or MariaDB)
- Node.js 22+ & npm 10+
- XAMPP (or equivalent local environment)

### Installation

1. **Clone/Navigate to project:**
   ```powershell
   cd D:\xampp\htdocs\Medicotrick_CMS\cms-app
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment:**
   - `.env` is auto-generated with defaults
   - Ensure `DB_CONNECTION=mysql`, `DB_DATABASE=cms`, `DB_USERNAME=root`
   - Adjust `APP_URL` as needed

4. **Set up database:**
   - Ensure MySQL is running:
     ```powershell
     Start-Process "D:\xampp\mysql\bin\mysqld.exe" -ArgumentList "--console" -WindowStyle Hidden
     ```
   - Create database (handled on first migration):
     ```bash
     php artisan migrate
     ```

5. **Link storage:**
   ```bash
   php artisan storage:link
   ```

6. **Build assets:**
   ```bash
   npm run build
   # Or for development with watch:
   npm run dev
   ```

### Running the App

```bash
php artisan serve
```

Access at `http://127.0.0.1:8000`

## Creating Your First Admin User

Register at `/register`, then promote to admin via Tinker:

```bash
php artisan tinker
```

```php
$user = User::where('email', 'your@email.com')->first();
$user->role = 'admin';
$user->save();
exit;
```

Then log in and visit `/admin/posts` to start managing content.

## Key Features

| Feature | Details |
|---------|---------|
| **Authentication** | Laravel Breeze (email/password) |
| **Roles** | admin, editor, author |
| **Posts** | Full CRUD with draft/publish workflow |
| **Categories** | Auto-created on post save, associated via UI |
| **Tags** | Many-to-many, comma-separated input |
| **Rich Editor** | CKEditor 4.22 (HTML, embeds, tables) |
| **Media** | Featured images, disk-based storage (`public/uploads`) |
| **SEO** | Meta title, meta description, auto-slug generation |
| **Public Blog** | Dynamic routes `/blog/{slug}` for published posts |
| **API** | JSON endpoints `/api/posts`, `/api/posts/{slug}` |
| **Multi-role Access** | Admin & editor can manage content; authors can view |

## Project Structure

```
cms-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/PostController.php      # POST CRUD
│   │   │   └── Api/PostApiController.php     # JSON endpoints
│   │   ├── Middleware/RoleMiddleware.php     # Role enforcement
│   │   └── Resources/PostResource.php        # API response shape
│   ├── Models/
│   │   ├── Post.php                          # Post, Category, Tags relations
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   └── User.php                          # Auth + roles
│   └── ...
├── database/
│   ├── migrations/
│   │   ├── *_create_users_table.php
│   │   ├── *_add_role_to_users_table.php
│   │   ├── *_create_posts_table.php          # Posts + SEO fields
│   │   ├── *_create_categories_table.php
│   │   ├── *_create_tags_table.php
│   │   └── *_create_post_tag_table.php       # M2M pivot
│   └── ...
├── resources/
│   ├── views/
│   │   ├── admin/posts/
│   │   │   ├── index.blade.php               # List posts
│   │   │   ├── create.blade.php              # New post + CKEditor
│   │   │   └── edit.blade.php                # Edit post + image preview
│   │   ├── blog/show.blade.php               # Public post view
│   │   └── layouts/
│   └── css/app.css
│   └── js/app.js
├── routes/
│   ├── web.php                               # Admin + blog routes
│   ├── api.php                               # JSON API routes
│   └── auth.php
├── storage/
│   └── app/public/uploads/                   # Featured images
├── public/
│   ├── storage -> ../storage/app/public      # Symlink for asset serving
│   └── build/                                # Vite build output
├── bootstrap/
│   └── app.php                               # Middleware & routing config
└── .env                                      # Database, app settings
```

## Admin Routes

All routes require `auth` + `role:admin,editor` middleware.

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/admin/posts` | List all posts (paginated) |
| GET | `/admin/posts/create` | Show post creation form |
| POST | `/admin/posts` | Store new post |
| GET | `/admin/posts/{id}/edit` | Show edit form |
| PUT | `/admin/posts/{id}` | Update post |
| DELETE | `/admin/posts/{id}` | Delete post |

## Public Routes

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/` | Homepage |
| GET | `/blog/{slug}` | View published post |
| GET | `/login` | Login form |
| GET | `/register` | Registration form |
| GET | `/dashboard` | User dashboard (auth only) |

## API Routes

No authentication required (public endpoints).

| Method | Route | Response |
|--------|-------|----------|
| GET | `/api/posts` | Paginated published posts (JSON) |
| GET | `/api/posts/{slug}` | Single post by slug (JSON) |

### API Response Example

```json
{
  "data": {
    "id": 1,
    "title": "Welcome to CustomCMS",
    "slug": "welcome-to-customcms",
    "content": "<p>HTML content here...</p>",
    "status": "published",
    "published_at": "2026-01-27T10:30:00Z",
    "author": {
      "id": 1,
      "name": "Admin User"
    },
    "category": {
      "id": 1,
      "name": "News",
      "slug": "news"
    },
    "tags": [
      { "id": 1, "name": "Laravel", "slug": "laravel" }
    ],
    "featured_image_url": "http://127.0.0.1:8000/storage/uploads/...",
    "meta": {
      "title": "Welcome – CustomCMS",
      "description": "Official welcome post."
    },
    "created_at": "2026-01-27T10:30:00Z",
    "updated_at": "2026-01-27T10:30:00Z"
  }
}
```

## Creating & Publishing Posts

1. Navigate to `/admin/posts/create`
2. Fill in:
   - **Title** (auto-generates slug)
   - **Content** (rich text via CKEditor)
   - **Status** (Draft or Published)
   - **Category** (type name; creates if not exists)
   - **Tags** (comma-separated; e.g., "php, laravel, cms")
   - **Featured Image** (optional image upload)
   - **Meta Title** & **Meta Description** (SEO)
   - **Publish Date** (optional; defaults to now if published)
3. Click **Save**
4. View at `/blog/{slug}` once published

## Using the Rich Text Editor

CKEditor 4.22 is loaded on create/edit pages:
- Supports HTML formatting, lists, links, tables, embeds
- Renders as-is: `{!! $post->content !!}` in Blade (unsafe for untrusted input)
- For production: consider sanitizing with `mewebstudio/purifier` or similar

## Media & Uploads

- **Location:** `storage/app/public/uploads/` (symlinked to `public/storage/`)
- **Serving:** `asset('storage/path/to/file')`
- **Constraints:** Max 2MB per image; JPEG, PNG, GIF, WebP
- **Deletion:** Old images are deleted when post is updated/deleted

## File Upload Workflow

1. User selects image in create/edit form
2. Controller validates (image, ≤2MB)
3. Stored in `storage/app/public/uploads/{filename}`
4. Path saved to posts.featured_image
5. Public URL: `https://yourapp.com/storage/uploads/{filename}`

## Database Schema

### Users Table

```sql
id, name, email, password, role (enum: admin|editor|author), email_verified_at, created_at, updated_at
```

### Posts Table

```sql
id, title, slug (unique), content (longText), status (enum: draft|published),
user_id (FK → users), category_id (nullable, indexed),
featured_image (nullable), meta_title, meta_description,
published_at (nullable), created_at, updated_at
```

### Categories Table

```sql
id, name, slug (unique), created_at, updated_at
```

### Tags Table

```sql
id, name, slug (unique), created_at, updated_at
```

### Post_Tag Pivot

```sql
post_id (FK), tag_id (FK), primary key (post_id, tag_id)
```

## Security Considerations

- **CSRF:** All forms protected via `@csrf` token
- **Auth:** Admin/editor routes require login + role check
- **XSS:** Content rendered raw (`{!! !!}`). Sanitize if accepting untrusted input (e.g., user comments)
- **File Upload:** Limited to images, max 2MB
- **Rate Limiting:** Not configured; add via middleware for production

## Customization & Next Steps

### Add Soft Deletes
```bash
php artisan make:migration add_soft_deletes_to_posts_table
```
Then add `use SoftDeletes;` to Post model.

### Enable Comments
Create a `Comment` model and `comments` table with `post_id` and `user_id` FKs.

### Add Search
Use Laravel Scout + Algolia, or basic MySQL LIKE queries.

### Caching
Add Redis and use `Cache::remember()` for expensive queries.

### Advanced Media
Integrate with AWS S3 or Cloudinary for CDN-backed images.

## Testing

```bash
php artisan test
```

Currently minimal tests. Add feature tests for:
- Post CRUD authorization
- API endpoint responses
- Slug generation uniqueness

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Routes not working | Run `php artisan route:clear` |
| Images not showing | Verify `php artisan storage:link` ran; check `public/storage` symlink |
| 403 on /admin/posts | Ensure user role is `admin` or `editor`; check via Tinker |
| CKEditor not loading | Check browser console for CDN errors; verify CSRF token |
| Database connection error | Ensure MySQL is running; check `.env` DB credentials |

## Deployment Checklist

- [ ] Set `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Set `APP_KEY` via `php artisan key:generate`
- [ ] Configure MySQL with strong credentials
- [ ] Set `FILESYSTEM_DISK=public` or use S3
- [ ] Configure mail (`.env` MAIL_* vars)
- [ ] Run `php artisan migrate --force`
- [ ] Build assets: `npm run build`
- [ ] Set up SSL/HTTPS
- [ ] Configure backup strategy for uploads & database

## License

MIT

## Support & Contributions

For issues, questions, or feature requests, refer to TECHNICAL_SPEC.md for detailed architecture.


In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
