# Earnings LLC - Data Annotation Platform

A complete PHP + MySQL data annotation platform with brand identification tasks, user management, and admin panel.

## Features

- User authentication with JWT tokens
- Brand identification tasks with earnings tracking
- Real-time wallet system
- Admin dashboard with statistics
- User management (view, edit, delete)
- Product management (CRUD operations)
- VIP tier system
- Transaction tracking

## Technology Stack

**Backend:**
- PHP 8.0+ with MySQL 5.7+
- JWT Authentication
- PDO for secure database access
- RESTful API architecture

## Quick Start

### 1. Setup Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE earnings_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE earnings_db;
SOURCE backend/database/schema.sql;
```

### 2. Configure Database Connection

Edit `backend/config/database.php` with your database credentials:

```php
private static $host = 'localhost';
private static $dbname = 'earnings_db';
private static $username = 'root';
private static $password = 'your_password';
```

### 3. Configure Web Server

For Apache, ensure the `.htaccess` file is in the project root and `mod_rewrite` is enabled.

For testing locally:
```bash
cd backend/public
php -S localhost:8000
```

The API will be available at: `http://localhost:8000/api`

### 4. Test the API

```bash
# Register a new user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123","full_name":"Test User"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'
```

## Default Admin Account

```
Email: admin@earnings.com
Password: admin123
```

**IMPORTANT: Change this password immediately in production!**

## Project Structure

```
project/
├── backend/
│   ├── config/          # Database & JWT configuration
│   ├── controllers/     # AuthController, TaskController, AdminController
│   ├── core/           # Database class
│   ├── database/       # schema.sql
│   ├── middleware/     # Authentication middleware
│   └── public/         # index.php (API entry point)
├── public/             # Static assets (images, logos)
└── .htaccess          # Apache rewrite rules
```

## API Endpoints

Base URL: `http://yourdomain.com/api`

### Authentication
- `POST /auth/register` - Register new user
- `POST /auth/login` - Login user
- `GET /auth/profile` - Get current user profile (requires token)

### Products & Tasks
- `GET /products` - Get active products for annotation
- `POST /tasks/submit` - Submit completed task (requires token)
- `GET /tasks` - Get user's task history (requires token)
- `GET /vip-tiers` - Get VIP membership tiers

### Admin Endpoints (requires admin token)
- `GET /admin/stats` - Platform statistics
- `GET /admin/users` - Get all users
- `PUT /admin/users/:id` - Update user
- `DELETE /admin/users/:id` - Delete user
- `GET /admin/products` - Get all products
- `POST /admin/products` - Create new product
- `PUT /admin/products/:id` - Update product
- `DELETE /admin/products/:id` - Delete product
- `GET /admin/tasks` - Get all tasks

## Authentication

All protected endpoints require a JWT token in the Authorization header:

```
Authorization: Bearer your_jwt_token_here
```

## Database Schema

The platform includes 5 main tables:
- **users** - User accounts, balances, VIP tiers
- **products** - Products for brand identification
- **tasks** - Completed annotation tasks
- **vip_tiers** - Membership tier definitions
- **transactions** - Earnings and withdrawal records

## Deployment

1. Upload files to your web host
2. Import database schema
3. Configure database credentials
4. Ensure Apache mod_rewrite is enabled
5. Point domain to project root directory

## Live Site

https://earningsllc.online

## Support

For issues or questions, check the Apache/PHP error logs at `/var/log/apache2/error.log` or your hosting provider's error log viewer.
