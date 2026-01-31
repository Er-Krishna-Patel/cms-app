# Medicotrick CMS - Setup & Contribution Guide

## Overview
This is a Laravel 12-based Content Management System with WordPress-style media management, featuring:
- Automatic thumbnail generation (3 sizes)
- WebP conversion with configurable quality
- Real-time media filtering and search
- Activity logging
- Role-based access control
- Post scheduling
- Category and tag management

## Quick Start for Developers

### 1. Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/Medicotrick_CMS.git
cd Medicotrick_CMS/cms-app
```

### 2. Database Setup
```bash
# Create database
mysql -u root -e "CREATE DATABASE cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the provided SQL file
mysql -u root cms < database_export.sql
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Update .env with your database credentials (if different from defaults)
# Default: DB_HOST=127.0.0.1, DB_USERNAME=root, DB_PASSWORD= (empty)
```

### 4. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Build Frontend Assets
```bash
npm run build
```

### 7. Run the Application
```bash
php artisan serve
```

Access the application at: `http://127.0.0.1:8000`

### Default Admin Login
- **Email**: admin@medicotrick.com
- **Password**: (Check database seeder or reset via CLI)

## Git Workflow

### Branch Strategy
- **main** → Production-ready code (stable)
- **developer** → Development/staging branch (current development)
- **feature/\*** → Feature branches (create from developer)

### Making Changes
```bash
# 1. Switch to developer branch
git checkout developer

# 2. Create feature branch
git checkout -b feature/your-feature-name

# 3. Make changes and commit
git add .
git commit -m "Descriptive commit message"

# 4. Push to remote
git push origin feature/your-feature-name

# 5. Create Pull Request to developer branch on GitHub
```

### Pulling Latest Changes
```bash
# Update developer branch
git checkout developer
git pull origin developer

# Update feature branch
git rebase developer
```

## Project Structure

```
cms-app/
├── app/
│   ├── Http/Controllers/     # Route controllers
│   ├── Models/              # Database models
│   ├── Services/            # Business logic
│   │   ├── MediaService.php    # Media operations
│   │   └── MediaUploader.php   # Image processing & compression
│   └── Providers/           # Service providers
├── resources/
│   ├── js/
│   │   ├── app.js                  # Main JS entry
│   │   └── media-upload.js        # File upload handler
│   ├── css/                 # Stylesheets
│   └── views/               # Blade templates
├── routes/                  # Route definitions
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/             # Data seeders
├── public/
│   ├── build/               # Compiled assets
│   └── storage/             # Uploaded files
├── config/                  # Configuration files
├── storage/                 # Logs and cache
├── database_export.sql      # Database dump for quick setup
└── DATABASE_SETUP.md        # Detailed database guide
```

## Key Features

### 1. Media Management
- Upload multiple files simultaneously
- Automatic WebP conversion
- Three-tier thumbnail system (300x300, 600x600, 1200x1200)
- Real-time search and filtering
- Configurable via `/admin/settings`

### 2. Post Management
- Create, edit, delete posts
- Draft and publish status
- Schedule posts for future publishing
- Category and tag association
- Activity tracking

### 3. Admin Dashboard
- User management
- Role assignment (admin, editor, viewer)
- Activity logs
- Settings management

## Configuration

### Media Settings
Access via `/admin/settings` → Media Settings:

- **Generate Thumbnails** (bool): Enable/disable thumbnail generation
- **Convert to WebP** (bool): Auto-convert images to WebP
- **WebP Quality** (1-100): Compression level
- **Thumbnail Sizes**: Customize width/height for 3 sizes
- **Max Upload Size**: KB limit per file

### Environment Variables
Key variables in `.env`:
```
APP_NAME=CustomCMS
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=log
```

## Development Commands

```bash
# Generate migrations
php artisan make:migration migration_name

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed --class=MediaSettingsSeeder

# Clear cache
php artisan cache:clear
php artisan config:clear

# Watch for JS/CSS changes (development)
npm run dev

# Build for production
npm run build

# Run tests
php artisan test
```

## Common Issues & Solutions

### 1. MySQL Connection Error
- Ensure MySQL is running: `services.msc` (Windows) → MySQL80
- Check credentials in `.env`
- Run: `mysql -u root` to test connection

### 2. Permission Denied on storage/
```bash
# Fix storage permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 3. Node modules issues
```bash
# Clear and reinstall
rm -rf node_modules package-lock.json
npm install
```

### 4. Asset compilation fails
```bash
# Clear build cache
rm -rf public/build/
npm run build
```

## Testing

### Run all tests
```bash
php artisan test
```

### Run specific test
```bash
php artisan test --filter=TestName
```

## Performance Optimization

### Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Optimization
```bash
php artisan optimize
php artisan migrate --force
```

## Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Run `composer install --no-dev`
- [ ] Generate secure `APP_KEY`
- [ ] Configure `APP_URL` correctly
- [ ] Set up HTTPS
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Run seeders: `php artisan db:seed --force`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`

## Support & Contributions

For issues or suggestions:
1. Check existing issues on GitHub
2. Create a detailed issue with steps to reproduce
3. Submit pull requests with clear descriptions
4. Follow the code style of existing files

## License
[Add your license information here]

---

**Happy coding! 🚀**
