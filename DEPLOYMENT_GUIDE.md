# Complete Deployment Guide for earningsllc.online

## Overview
This guide will help you deploy your Data Annotation Platform to https://earningsllc.online/

## Pre-Deployment Checklist

✅ Domain: https://earningsllc.online/
✅ Database: u800179901_70
✅ Database User: u800179901_70
✅ Database Password: Cpanel@2025
✅ Build completed successfully

## Step-by-Step Deployment Instructions

### Step 1: Upload API Files

1. **Connect to cPanel File Manager** or use FTP
2. **Navigate to** `public_html/`
3. **Create folder** `api` if it doesn't exist
4. **Upload the entire `api/` folder** from your project to `public_html/api/`
   - Make sure `.htaccess` is uploaded
   - Make sure `.env` file is uploaded
   - Verify all PHP files are uploaded

### Step 2: Upload Frontend Files

1. **In cPanel File Manager**, navigate to `public_html/`
2. **Upload all files from the `dist/` folder** to `public_html/`
   - This includes: `index.html`, `assets/` folder, and all image files
   - Make sure the root `.htaccess` is uploaded

### Step 3: Configure Database

1. **Log into phpMyAdmin** from cPanel
2. **Select database** `u800179901_70`
3. **Import SQL file**: Click "Import" tab
4. **Choose file**: `api/database/migrations.sql`
5. **Click "Go"** to execute
6. **Verify**: Check that these tables were created:
   - users
   - tasks
   - submissions
   - products
   - payment_gateways

### Step 4: Verify File Permissions

1. **In cPanel File Manager**, select `public_html/api/.env`
2. **Right-click** → **Change Permissions**
3. **Set to 644** (Read & Write for owner, Read for others)
4. **Verify** `.htaccess` files are readable (644)

### Step 5: Test the API

1. **Visit**: https://earningsllc.online/api/test.php
2. **Expected Response** (JSON):
   ```json
   {
     "status": "ok",
     "message": "API is working",
     "php_version": "8.x.x",
     "env_file": "exists",
     "db_host": "localhost",
     "db_name": "u800179901_70",
     "db_test": "connected successfully",
     "cors_origins": "https://earningsllc.online"
   }
   ```
3. **If `db_test` shows an error**:
   - Check database credentials in `api/.env`
   - Verify database exists in cPanel → MySQL Databases
   - Ensure database user has all privileges

### Step 6: Test the Frontend

1. **Visit**: https://earningsllc.online/
2. **You should see**: The landing page with "AI Data Annotation Platform"
3. **Test signup/login** functionality

### Step 7: Login as Admin

**Default Admin Credentials** (after database import):
- **Email**: `admin@example.com`
- **Password**: `admin123`

**IMPORTANT**: Change this password immediately after first login!

## File Structure on Server

```
public_html/
├── api/
│   ├── .env                    # Environment configuration
│   ├── .htaccess              # URL rewriting rules
│   ├── index.php              # API entry point
│   ├── test.php               # Diagnostic endpoint
│   ├── app/
│   │   ├── Database.php
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── config/
│   │   ├── app.php
│   │   └── database.php
│   └── database/
│       └── migrations.sql
├── .htaccess                  # Frontend routing
├── index.html                 # React app entry
└── assets/                    # CSS, JS, images
```

## Troubleshooting

### Problem: 500 Internal Server Error

**Solution 1**: Check `.env` file exists
```bash
# File: public_html/api/.env
# Verify it contains:
DB_HOST=localhost
DB_DATABASE=u800179901_70
DB_USERNAME=u800179901_70
DB_PASSWORD=Cpanel@2025
```

**Solution 2**: Check PHP error logs
1. Go to cPanel → Errors
2. Look for recent PHP errors
3. Common issues:
   - File permissions (should be 644)
   - Missing `.env` file
   - Database connection errors

**Solution 3**: Verify `.htaccess` files
1. Root `.htaccess` should route API requests correctly
2. API `.htaccess` should rewrite URLs to index.php

### Problem: API returns "Not found"

**Check**:
1. `.htaccess` in `api/` folder exists
2. mod_rewrite is enabled (usually is on cPanel)
3. URLs match the pattern: `https://earningsllc.online/api/auth/signin`

### Problem: CORS errors

**Check**:
1. `api/.env` has: `CORS_ALLOWED_ORIGINS=https://earningsllc.online`
2. No trailing slash in the URL
3. Headers are being sent (check in browser DevTools → Network)

### Problem: Database connection failed

**Check**:
1. Database exists: cPanel → MySQL Databases
2. User has privileges: cPanel → MySQL Databases → Current Users
3. Password is correct in `api/.env`
4. Database imported successfully

## Security Recommendations

1. **Change Default Admin Password** immediately
2. **Update JWT Secret** in `api/.env`:
   ```
   JWT_SECRET=your-secure-random-string-here-at-least-32-chars
   ```
3. **Set APP_DEBUG to false** in production:
   ```
   APP_DEBUG=false
   ```
4. **Enable HTTPS** (usually automatic with cPanel)
5. **Regular backups** of database and files

## Testing Checklist

- [ ] Landing page loads at https://earningsllc.online/
- [ ] Test API endpoint returns success
- [ ] Signup creates new user
- [ ] Login works with admin credentials
- [ ] Admin dashboard loads
- [ ] Can create/manage products
- [ ] Can generate tasks
- [ ] User can complete tasks
- [ ] Payment methods display correctly

## Support

If you encounter issues:
1. Check `https://earningsllc.online/api/test.php` first
2. Review PHP error logs in cPanel
3. Verify all files uploaded correctly
4. Check database imported successfully

## Next Steps After Deployment

1. Change admin password
2. Update JWT secret key
3. Add your own products
4. Generate annotation tasks
5. Test complete user workflow
6. Invite test users
