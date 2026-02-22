# Deployment Guide - PHP + MySQL Platform

Complete guide to deploy your Earnings LLC platform to a web host.

## Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- FTP/SFTP access to your hosting

## Step 1: Prepare Files

1. Download all project files to your local computer
2. Ensure you have these folders:
   - `backend/` - PHP application
   - `public/` - Images and assets
   - `.htaccess` - Apache configuration

## Step 2: Upload Files

### Via FTP/SFTP:

1. Connect to your hosting account
2. Navigate to `public_html` or your domain's root directory
3. Upload all files and folders:
   ```
   /backend
   /public
   .htaccess
   .env (optional)
   README.md
   ```

### Via cPanel File Manager:

1. Login to cPanel
2. Open "File Manager"
3. Navigate to `public_html`
4. Click "Upload" and select all project files
5. Ensure folder structure is maintained

## Step 3: Create MySQL Database

### Via cPanel:

1. Go to "MySQL Databases"
2. Create a new database (e.g., `earnings_db`)
3. Create a MySQL user with a strong password
4. Add user to the database with ALL PRIVILEGES
5. Note down:
   - Database name
   - Username
   - Password
   - Host (usually `localhost`)

### Via phpMyAdmin:

1. Login to phpMyAdmin
2. Click "New" to create database
3. Name it `earnings_db`
4. Set collation to `utf8mb4_unicode_ci`
5. Create a user with appropriate privileges

## Step 4: Import Database Schema

### Via phpMyAdmin:

1. Select your database
2. Click "Import" tab
3. Choose file: `backend/database/schema.sql`
4. Click "Go"
5. Verify tables are created:
   - users
   - products
   - tasks
   - vip_tiers
   - transactions

### Via MySQL Command Line:

```bash
mysql -u your_username -p your_database < backend/database/schema.sql
```

## Step 5: Configure Database Connection

Edit `backend/config/database.php`:

```php
private static $host = 'localhost';           // Usually localhost
private static $dbname = 'earnings_db';       // Your database name
private static $username = 'your_db_user';    // Your database user
private static $password = 'your_password';   // Your database password
```

**IMPORTANT:** Never share or commit these credentials!

## Step 6: Configure JWT Secret

Edit `backend/config/jwt.php`:

```php
private static $secret = 'change-this-to-a-random-string-32-chars-long';
```

Generate a random secret:
```bash
openssl rand -base64 32
```

## Step 7: Set File Permissions

Set proper permissions via FTP or cPanel:

```
backend/               755
backend/config/        755
backend/controllers/   755
backend/core/          755
backend/database/      755
backend/middleware/    755
backend/public/        755
public/                755
```

All PHP files should be: `644`

## Step 8: Configure .htaccess

Ensure `.htaccess` exists in root directory:

```apache
RewriteEngine On

# Redirect all API requests to backend
RewriteRule ^api/(.*)$ backend/public/index.php [L]

# Prevent directory browsing
Options -Indexes

# Enable CORS
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>
```

## Step 9: Test API Endpoints

### Test Registration:

```bash
curl -X POST https://yourdomain.com/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"test123","full_name":"Test User"}'
```

Expected response:
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "email": "test@test.com",
    "role": "user"
  }
}
```

### Test Login:

```bash
curl -X POST https://yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@earnings.com","password":"admin123"}'
```

### Test Products:

```bash
curl https://yourdomain.com/api/products
```

## Step 10: Default Admin Account

Login with:
```
Email: admin@earnings.com
Password: admin123
```

**⚠️ CRITICAL:** Change this password immediately!

To change admin password, hash a new password:

```php
<?php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
?>
```

Then update in database:
```sql
UPDATE users SET password = 'hashed_password_here' WHERE email = 'admin@earnings.com';
```

## Troubleshooting

### Error: "Database connection failed"

**Solution:**
- Check database credentials in `backend/config/database.php`
- Verify database exists
- Check user has proper privileges
- Confirm host is correct (usually `localhost`)

### Error: "404 Not Found" on API calls

**Solution:**
- Ensure `.htaccess` is in root directory
- Check Apache `mod_rewrite` is enabled
- Verify file permissions (755 for directories, 644 for files)
- Check Apache error logs

### Error: "Access denied"

**Solution:**
- Verify MySQL user has correct privileges
- Try `localhost` vs `127.0.0.1` as host
- Check MySQL user is allowed from the web server host

### Error: "JWT token invalid"

**Solution:**
- Check `backend/config/jwt.php` secret key is set
- Ensure token is being sent in `Authorization: Bearer token` header
- Verify token hasn't expired (24 hour default)

### Enable Error Reporting

Add to `backend/public/index.php` (top of file):

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Remove in production!**

## Security Checklist

- [ ] Change default admin password
- [ ] Set strong JWT secret key
- [ ] Use strong database password
- [ ] Remove error reporting in production
- [ ] Ensure `.env` is not web-accessible
- [ ] Set proper file permissions
- [ ] Enable HTTPS/SSL certificate
- [ ] Backup database regularly

## Maintenance

### Database Backup

```bash
mysqldump -u username -p earnings_db > backup.sql
```

### Update Product Images

Upload images to `public/products/` folder and update database:

```sql
UPDATE products SET image_url = '/products/new_image.jpg' WHERE id = 1;
```

### Check Error Logs

- cPanel: "Errors" section
- File: `/var/log/apache2/error.log`
- PHP errors: Check hosting control panel

## Performance Tips

1. Enable MySQL query caching
2. Use PHP opcache
3. Enable Gzip compression in Apache
4. Optimize images before uploading
5. Consider CDN for static assets

## Support

For deployment issues:
1. Check Apache error logs
2. Verify PHP version (8.0+)
3. Confirm MySQL version (5.7+)
4. Test database connection separately
5. Check file permissions

---

**Deployment Complete!** Your platform should now be live at `https://yourdomain.com`
