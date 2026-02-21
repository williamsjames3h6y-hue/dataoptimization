# Data Annotation Platform - Setup Instructions

## MySQL Database Configuration

Your database is already configured with these credentials:

- **Database Name:** `u800179901_70`
- **Username:** `u800179901_70`
- **Password:** `Investocc@2312`
- **Host:** `127.0.0.1`
- **Port:** `3306`

## Step 1: Import Database Schema

You need to import the database schema to create all necessary tables.

### Option A: Using phpMyAdmin
1. Log in to your cPanel
2. Open phpMyAdmin
3. Select database `u800179901_70`
4. Click on "Import" tab
5. Choose file: `api/database/migrations.sql`
6. Click "Go" to import

### Option B: Using MySQL Command Line
```bash
mysql -u u800179901_70 -p u800179901_70 < api/database/migrations.sql
```
Enter password when prompted: `Investocc@2312`

## Step 2: Verify Database Tables

After importing, you should have these tables:
- users
- user_profiles
- wallets
- products
- tasks
- task_questions
- task_submissions
- payment_methods
- payment_gateways
- withdrawal_requests
- earnings_history
- sessions

## Step 3: Test Default Admin Login

After database import, test the login:
- **Email:** `admin@example.com`
- **Password:** `admin123`

**IMPORTANT:** Change this password immediately after first login!

## Step 4: Start Development Server

### Backend (API)
```bash
cd api
php -S localhost:8000
```

### Frontend
```bash
npm run dev
```

The app will be available at `http://localhost:5173`

## API Endpoints

All endpoints are prefixed with `/api/`

### Authentication
- `POST /api/auth/signup` - Register new user
- `POST /api/auth/signin` - Login
- `POST /api/auth/signout` - Logout
- `GET /api/auth/user` - Get current user

### Tasks (User Routes)
- `GET /api/tasks` - Get available tasks
- `POST /api/tasks/{id}/submit` - Submit task
- `GET /api/tasks/stats` - Get user statistics

### Admin Routes
- `GET /api/admin/stats` - Platform statistics
- `GET /api/admin/users` - List all users
- `PUT /api/admin/users/{id}/role` - Update user role
- `DELETE /api/admin/users/{id}` - Delete user
- `GET /api/admin/products` - List products
- `POST /api/admin/products` - Create product
- `PUT /api/admin/products/{id}` - Update product
- `DELETE /api/admin/products/{id}` - Delete product
- `POST /api/admin/tasks/generate` - Generate tasks
- `GET /api/admin/payment-gateways` - List payment gateways
- `POST /api/admin/payment-gateways` - Create gateway
- `PUT /api/admin/payment-gateways/{id}` - Update gateway
- `DELETE /api/admin/payment-gateways/{id}` - Delete gateway

## Configuration Files

### Backend Configuration: `api/.env`
```env
DB_DATABASE=u800179901_70
DB_USERNAME=u800179901_70
DB_PASSWORD=Investocc@2312
```

### Frontend Configuration: `.env`
```env
VITE_API_URL=http://localhost:8000
```

### Production Configuration: `.env.production`
```env
VITE_API_URL=https://earningsllc.online/api
VITE_SUPABASE_URL=https://0ec90b57d6e95fcbda19832f.supabase.co
VITE_SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

## Production Deployment

### 1. Upload Files to cPanel
- Upload `api/` folder to `public_html/api/`
- Upload `dist/` folder contents to `public_html/`

### 2. Ensure .htaccess is Configured
The `api/.htaccess` file should be present and contains URL rewriting rules.

### 3. Update Production Environment
Edit `api/.env` on server:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://earningsllc.online
CORS_ALLOWED_ORIGINS=https://earningsllc.online
```

### 4. Build Frontend for Production
```bash
npm run build
```

Upload the contents of `dist/` folder to your web root.

## Troubleshooting

### Database Connection Failed
- Verify MySQL service is running
- Check credentials in `api/.env`
- Ensure database `u800179901_70` exists
- Test connection: `mysql -u u800179901_70 -p`

### 404 Errors on API Routes
- Check `api/.htaccess` is uploaded
- Verify Apache `mod_rewrite` is enabled
- Ensure all routes start with `/api/`

### CORS Errors
- Update `CORS_ALLOWED_ORIGINS` in `api/.env`
- Check if Apache `mod_headers` is enabled

### Authentication Issues
- Clear browser localStorage
- Check JWT_SECRET is set in `api/.env`
- Verify sessions table exists in database

## Security Checklist

- [ ] Change default admin password
- [ ] Generate strong JWT_SECRET (64+ characters)
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS in production
- [ ] Restrict CORS to specific domains
- [ ] Regular database backups
- [ ] Keep PHP and MySQL updated

## Sample Products Included

The database schema includes 5 sample products:
1. Wireless Headphones (TechBrand)
2. Running Shoes (SportPro)
3. Smart Watch (TechBrand)
4. Backpack (TravelGear)
5. Coffee Maker (HomePlus)

Use the admin dashboard to generate annotation tasks from these products.

## Support

For issues:
1. Check PHP error logs
2. Check MySQL error logs
3. Check browser console
4. Enable `APP_DEBUG=true` for detailed errors
