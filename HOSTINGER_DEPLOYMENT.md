# Hostinger hPanel Deployment Guide

## Prerequisites
- Hostinger hosting account with PHP 7.4+ and MySQL
- Domain: earningsllc.online
- FTP/File Manager access
- MySQL database created in hPanel

## Step 1: Prepare Your Files

1. Build the frontend (already done):
   ```bash
   npm run build
   ```

2. Your deployment files are in the `dist` folder and `api` folder

## Step 2: Upload Files to Hostinger

### Using File Manager (Recommended)

1. Log in to Hostinger hPanel
2. Go to **Files** > **File Manager**
3. Navigate to `public_html` folder
4. Delete all existing files in `public_html`
5. Upload ALL files from the `dist` folder to `public_html`
6. Upload the `.htaccess` file from project root to `public_html`
7. Upload the entire `api` folder to `public_html`

Your structure should look like:
```
public_html/
├── index.html
├── assets/
├── products/
├── .htaccess
└── api/
    ├── app/
    ├── config/
    ├── database/
    ├── public/
    ├── routes/
    ├── vendor/
    ├── .htaccess
    ├── .env
    └── composer.json
```

### Using FTP (Alternative)

1. Connect via FTP to your Hostinger account
2. Navigate to `public_html`
3. Upload all files as described above

## Step 3: Set Up MySQL Database

1. In hPanel, go to **Databases** > **MySQL Databases**
2. Note your database credentials:
   - Database Name: `u800179901_70`
   - Database User: `u800179901_70`
   - Database Password: (your password)
   - Database Host: `localhost` or `127.0.0.1`

3. Update the API configuration:
   - Edit `public_html/api/.env` file
   - Update database credentials:
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=u800179901_70
   DB_USER=u800179901_70
   DB_PASSWORD=your_actual_password_here
   JWT_SECRET=generate_a_random_secret_key_here
   ```

## Step 4: Install PHP Dependencies

### Option A: Using SSH (if available)

1. Connect via SSH
2. Navigate to your API folder:
   ```bash
   cd public_html/api
   ```
3. Install Composer dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

### Option B: Manual Upload (if no SSH)

1. On your local machine, run:
   ```bash
   cd api
   composer install --no-dev --optimize-autoloader
   ```
2. Upload the generated `vendor` folder to `public_html/api/`

## Step 5: Run Database Migrations

### Option A: Using Browser

1. Navigate to: `https://earningsllc.online/api/database/setup.php`
2. This will create all tables and seed initial data
3. You should see success messages

### Option B: Using SSH

```bash
cd public_html/api
php database/setup.php
```

### Option C: Using phpMyAdmin

1. Go to **Databases** > **phpMyAdmin** in hPanel
2. Select your database
3. Import each migration file manually by running the SQL:

Run these in order:
1. `001_create_users_table.php`
2. `002_create_wallets_table.php`
3. `003_create_products_table.php`
4. `004_create_brand_identification_tasks_table.php`
5. `005_create_earnings_table.php`
6. `006_create_payment_methods_table.php`
7. `007_create_payment_gateways_table.php`

Then run the seeder to populate products.

## Step 6: Configure Domain

1. In hPanel, ensure your domain `earningsllc.online` points to `public_html`
2. Enable SSL certificate (hPanel usually provides free SSL)

## Step 7: Update Frontend Environment

The frontend is already configured to use:
```
VITE_API_URL=https://earningsllc.online
```

## Step 8: Test Your Deployment

1. Visit: `https://earningsllc.online`
2. Test signup/login functionality
3. Test the API endpoints:
   - `https://earningsllc.online/api/auth/signup`
   - `https://earningsllc.online/api/tasks`

## Step 9: Create Admin Account

After deployment, create an admin account:

1. Sign up normally through the website
2. Go to phpMyAdmin
3. Find your user in the `users` table
4. Update the `role` field from `user` to `admin`

```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

## Troubleshooting

### 500 Internal Server Error
- Check file permissions (should be 644 for files, 755 for folders)
- Ensure `.htaccess` files are uploaded
- Check error logs in hPanel

### API Not Working
- Verify database credentials in `api/.env`
- Ensure `vendor` folder exists in `api/`
- Check that Composer dependencies are installed

### Database Connection Failed
- Verify database credentials
- Ensure database user has full privileges
- Check that database exists

### CORS Errors
- The API already includes CORS headers
- Ensure the `.htaccess` file is properly uploaded

## Important Files to Secure

Update these before going live:

1. **api/.env**
   - Change `JWT_SECRET` to a strong random string
   - Update database password

2. **File Permissions**
   ```
   chmod 644 api/.env
   chmod 755 api
   chmod 755 public_html
   ```

## Post-Deployment Checklist

- [ ] Website loads at https://earningsllc.online
- [ ] SSL certificate is active
- [ ] Login/Signup works
- [ ] API endpoints respond correctly
- [ ] Admin panel accessible
- [ ] Database tables created successfully
- [ ] Product images load correctly

## Support

If you encounter issues:
1. Check Hostinger error logs in hPanel
2. Enable error reporting temporarily:
   - Add to `api/public/index.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
3. Check browser console for frontend errors
4. Verify API responses in Network tab

Your application is now ready to deploy on Hostinger!
