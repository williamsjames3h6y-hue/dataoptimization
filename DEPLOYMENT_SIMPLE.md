# Simple Hostinger Deployment Guide

## Your Project Structure (Clean & Simple)

```
public_html/
├── .htaccess              (Apache routing config)
├── .env                   (Environment configuration - IMPORTANT!)
├── index.php              (Optional entry point)
├── package.json
├── composer.json
├── api/                   (Backend - Laravel-style PHP)
│   ├── .htaccess
│   ├── .env              (Database credentials)
│   ├── composer.json
│   ├── vendor/           (PHP dependencies)
│   ├── app/
│   │   └── Http/
│   │       └── Controllers/
│   ├── config/
│   │   ├── database.php
│   │   └── jwt.php
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── setup.php
│   ├── public/
│   │   └── index.php
│   └── routes/
│       └── api.php
├── dist/                  (Built React app)
│   ├── index.html
│   └── assets/
└── public/                (Static assets)
    ├── products/
    │   ├── P1.jpg
    │   ├── P2.jpg
    │   └── ...
    └── *.jpg (logo, AI images, etc)
```

## Step-by-Step Deployment

### 1. Upload Files to Hostinger

**Via File Manager:**
1. Login to hPanel
2. Go to **Files > File Manager**
3. Navigate to `public_html`
4. **Delete all existing files** in `public_html`
5. Upload these folders/files:
   - `api/` (entire folder)
   - `dist/` (entire folder)
   - `public/` (entire folder)
   - `.htaccess`
   - `package.json`
   - `composer.json`

### 2. Configure Database

1. In hPanel, go to **Databases > MySQL Databases**
2. Your database details:
   - **Database Name:** `u800179901_70`
   - **Database User:** `u800179901_70`
   - **Database Host:** `localhost`
   - **Password:** (use your actual password)

3. Edit `api/.env` file in File Manager:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=u800179901_70
DB_USER=u800179901_70
DB_PASSWORD=YOUR_ACTUAL_PASSWORD

JWT_SECRET=RANDOM_SECRET_KEY_CHANGE_THIS
JWT_EXPIRATION=86400
```

**IMPORTANT:** Change `JWT_SECRET` to a random string!

### 3. Install PHP Dependencies

**Option A: Via SSH (if available)**
```bash
cd public_html/api
composer install --no-dev --optimize-autoloader
```

**Option B: Upload vendor folder**
1. On your computer, run:
   ```bash
   cd api
   composer install --no-dev --optimize-autoloader
   ```
2. Upload the generated `vendor/` folder to `public_html/api/`

### 4. Setup Database

**Method 1: Browser (Easiest)**
Visit: `https://earningsllc.online/api/database/setup.php`

**Method 2: SSH**
```bash
cd public_html/api
php database/setup.php
```

**Method 3: phpMyAdmin Manual**
Run each migration SQL in order:
1. `001_create_users_table.php`
2. `002_create_wallets_table.php`
3. `003_create_products_table.php`
4. `004_create_brand_identification_tasks_table.php`
5. `005_create_earnings_table.php`
6. `006_create_payment_methods_table.php`
7. `007_create_payment_gateways_table.php`

### 5. Create Admin Account

1. Visit `https://earningsllc.online`
2. Sign up with your email
3. Go to **Databases > phpMyAdmin** in hPanel
4. Select database `u800179901_70`
5. Run this SQL:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

### 6. Test Everything

- [ ] Visit `https://earningsllc.online` - Should load landing page
- [ ] Sign up / Login works
- [ ] User dashboard loads
- [ ] Products visible
- [ ] Tasks work
- [ ] Admin panel accessible at `/admin`

## File Permissions

Set correct permissions via File Manager:
- Folders: `755`
- Files: `644`
- `api/.env`: `644` (or `600` for extra security)

## Troubleshooting

### "500 Internal Server Error"
- Check `.htaccess` files are uploaded
- Verify file permissions (755 for folders, 644 for files)
- Check error logs in hPanel

### "Database Connection Failed"
- Verify credentials in `api/.env`
- Ensure database exists in hPanel
- Test database connection in phpMyAdmin

### "API Not Found" / 404 Errors
- Ensure `api/vendor/` folder exists
- Check `.htaccess` RewriteEngine is working
- Verify `composer install` completed successfully

### "CORS Errors"
- API already includes CORS headers
- Check browser console for exact error

## Security Checklist

Before going live:
- [ ] Change `JWT_SECRET` in `api/.env` to a strong random string
- [ ] Remove or protect `api/database/setup.php` after first run
- [ ] Set secure file permissions
- [ ] Enable SSL certificate (free in hPanel)
- [ ] Test all functionality
- [ ] Create backup of database

## What Each Folder Does

- **api/** - Backend PHP code (like Laravel)
  - Handles authentication, tasks, earnings
  - Processes API requests
  - Manages database operations

- **dist/** - Frontend React app (built)
  - The main website interface
  - Single Page Application (SPA)
  - Serves dashboard, landing page

- **public/** - Static files
  - Product images
  - Brand logos
  - Other assets

## Support

Check these if issues occur:
1. Hostinger error logs (hPanel > Files > Error Logs)
2. Browser console (F12 > Console tab)
3. Network tab (F12 > Network) for API errors
4. Database connection via phpMyAdmin

Your site will be live at: **https://earningsllc.online**
