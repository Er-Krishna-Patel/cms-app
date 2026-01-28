# Database Setup Guide for Developers

## Quick Start

To get the database running on your local machine, follow these steps:

### Prerequisites
- MySQL/MariaDB running
- XAMPP or similar local server

### Setup Steps

1. **Create the Database**
   ```bash
   mysql -u root -e "CREATE DATABASE cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

2. **Import the Database**
   ```bash
   mysql -u root cms < database_export.sql
   ```

3. **Configure Environment**
   
   Copy `.env.example` to `.env` (if not already done):
   ```bash
   cp .env.example .env
   ```
   
   Update database credentials in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

6. **Build Frontend Assets**
   ```bash
   npm run build
   ```

7. **Run the Application**
   ```bash
   php artisan serve
   ```

   The app will be available at `http://127.0.0.1:8000`

## Default Login Credentials

Check the `database_export.sql` file or the seeders for default admin credentials.

## Database Structure

The `database_export.sql` file contains:
- All tables with structure and data
- Users table with admin account
- Posts, Categories, Tags
- Media files metadata
- Activity logs
- Settings
- All relationships and constraints

## Troubleshooting

### Database Import Errors
- Ensure MySQL is running
- Check character encoding (should be utf8mb4)
- Verify database user has proper permissions

### Migration Issues
- Run `php artisan migrate` if tables are missing
- Run `php artisan migrate:fresh --seed` to reset with seeders

## Branches

- **main**: Production-ready code
- **developer**: Development/staging branch

Always work on `developer` and create pull requests to `main` for production releases.
