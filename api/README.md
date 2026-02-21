# Data Annotation Platform - Laravel API

This is a pure PHP API built with a Laravel-like structure that connects to MySQL database.

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled (or Nginx)
- PDO MySQL extension

## Local Development Setup

### 1. Database Setup

Create a MySQL database and import the schema:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE data_annotation;
USE data_annotation;
SOURCE database/schema.sql;
```

### 2. Configure Environment

Copy the `.env.example` file to `.env` and update your database credentials:

```bash
cp .env.example .env
```

Edit `.env`:
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_annotation
DB_USERNAME=root
DB_PASSWORD=your_password
JWT_SECRET=your-random-secret-key-here
```

### 3. Start PHP Server

```bash
cd public
php -S localhost:8000
```

The API will be available at `http://localhost:8000`

### 4. Update Frontend Config

Update your React frontend `.env` file:
```
VITE_API_URL=http://localhost:8000
```

## cPanel Deployment

### 1. Upload Files

Upload all files in the `api` folder to your cPanel hosting:
- Upload to: `public_html/api/`

### 2. Database Setup

1. Create a MySQL database in cPanel
2. Import `database/schema.sql` using phpMyAdmin
3. Note your database name, username, and password

### 3. Configure .env

Edit `api/.env` file with your cPanel database credentials:

```
APP_NAME="Data Annotation Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password

JWT_SECRET=generate-random-32-character-string

CORS_ALLOWED_ORIGINS=https://yourdomain.com
```

### 4. Configure .htaccess

The `.htaccess` files are already configured. Ensure:
- `api/.htaccess` redirects to `public/`
- `api/public/.htaccess` handles URL rewriting

### 5. Set Permissions

Set proper permissions:
```bash
chmod 755 api/storage/logs
```

### 6. Update Frontend

Update your React frontend `.env.production`:
```
VITE_API_URL=https://yourdomain.com/api
```

## API Endpoints

### Authentication
- `POST /api/auth/signup` - Register new user
- `POST /api/auth/signin` - Login
- `POST /api/auth/signout` - Logout
- `GET /api/auth/user` - Get current user

### Tasks
- `GET /api/tasks` - Get available tasks
- `POST /api/tasks/:id/submit` - Submit task
- `GET /api/tasks/stats` - Get user stats

### Admin (Requires admin role)
- `GET /api/admin/stats` - Platform statistics
- `GET /api/admin/users` - List all users
- `PUT /api/admin/users/:id/role` - Update user role
- `DELETE /api/admin/users/:id` - Delete user
- `GET /api/admin/products` - List products
- `POST /api/admin/products` - Create product
- `PUT /api/admin/products/:id` - Update product
- `DELETE /api/admin/products/:id` - Delete product
- `POST /api/admin/tasks/generate` - Generate tasks
- `GET /api/admin/payment-gateways` - List payment gateways
- `POST /api/admin/payment-gateways` - Create gateway
- `PUT /api/admin/payment-gateways/:id` - Update gateway
- `DELETE /api/admin/payment-gateways/:id` - Delete gateway

## Default Admin Account

After importing the database schema, you can login with:
- **Email**: admin@example.com
- **Password**: admin123

**IMPORTANT**: Change this password immediately in production!

## Directory Structure

```
api/
├── app/
│   ├── Controllers/     # API Controllers
│   ├── Middleware/      # Authentication middleware
│   └── Database.php     # Database connection
├── config/              # Configuration files
├── database/            # Database schema
├── public/              # Public web root
│   ├── .htaccess       # URL rewriting
│   └── index.php       # Application entry point
├── storage/            # Logs and temp files
├── .env                # Environment configuration
└── .htaccess          # Root redirects

```

## Troubleshooting

### 500 Internal Server Error

1. Check `.htaccess` is working (mod_rewrite enabled)
2. Verify database credentials in `.env`
3. Check `storage/logs/` permissions (755)
4. Enable `APP_DEBUG=true` to see error details

### Database Connection Failed

1. Verify MySQL is running
2. Check database credentials in `.env`
3. Ensure database exists and schema is imported
4. Test connection with `mysql -u username -p database`

### CORS Errors

1. Check `CORS_ALLOWED_ORIGINS` in `.env`
2. Verify `.htaccess` headers are enabled
3. Ensure mod_headers is enabled in Apache

### 404 Errors

1. Verify `.htaccess` files are uploaded
2. Check mod_rewrite is enabled
3. Ensure requests go to `public/index.php`

## Security Notes

1. **Change JWT_SECRET**: Generate a random 32+ character string
2. **Change admin password**: Login and update immediately
3. **Set APP_DEBUG=false** in production
4. **Restrict CORS**: Set specific domain instead of `*`
5. **Use HTTPS**: Always use SSL certificate in production
6. **Database backups**: Regular automated backups
7. **Keep PHP updated**: Use latest stable PHP version

## Support

For issues or questions, check:
1. PHP error logs
2. MySQL error logs
3. Browser console for API errors
4. Database connection test
