# Pure PHP + MySQL Application Setup

This is a **100% pure PHP application** with no npm/build dependencies.

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled

## Installation Steps

### 1. Database Setup

```bash
# Create database and import schema
mysql -u root -p < backend/database/schema.sql
```

Or manually:
1. Create a database named `earnings_db`
2. Import `backend/database/schema.sql`

### 2. Configure Database Connection

Edit `backend/config/database.php` with your MySQL credentials:

```php
private static $host = 'localhost';
private static $dbname = 'earnings_db';
private static $username = 'root';
private static $password = 'your_password';
```

### 3. Deploy

Upload all files to your web server or run locally:

```bash
# If using PHP built-in server (for testing)
php -S localhost:8000 -t public
```

For Apache, point your document root to the `public` directory.

## Default Credentials

**Admin Account:**
- Email: admin@earnings.com
- Password: admin123

## File Structure

```
project/
├── public/
│   ├── index.html          # Frontend interface
│   ├── logo.jpg            # Logo image
│   └── products/           # Product images
├── backend/
│   ├── config/             # Configuration files
│   ├── controllers/        # API controllers
│   ├── core/               # Core classes
│   ├── middleware/         # Authentication middleware
│   ├── database/           # SQL schema
│   └── public/
│       └── index.php       # API entry point
└── .htaccess               # Apache rewrite rules

```

## API Endpoints

All endpoints are accessible via:
- Path-based: `/backend/public/index.php?action=<action>`
- RESTful: `/auth/register`, `/auth/login`, etc.

### Public Endpoints
- POST `/backend/public/index.php?action=register` - Register new user
- POST `/backend/public/index.php?action=login` - Login

### Protected Endpoints (require JWT token)
- GET `/backend/public/index.php?action=profile` - Get user profile
- GET `/products` - List products
- POST `/tasks/submit` - Submit task
- GET `/tasks` - Get user tasks

## No Build Process Required

This application runs directly with PHP - no compilation, transpilation, or build steps needed!
