# Laravel + MySQL Backend Deployment Guide

Your application has been migrated from Supabase to Laravel (PHP) with MySQL database.

## What Changed

- **Backend**: Pure PHP API with Laravel-like structure
- **Database**: MySQL instead of Supabase PostgreSQL
- **Authentication**: JWT tokens instead of Supabase Auth
- **Frontend**: React code remains EXACTLY the same - only API endpoint changed

## File Structure

```
project/
├── api/                    # Laravel-like PHP backend
│   ├── app/
│   │   ├── Controllers/   # API logic
│   │   ├── Middleware/    # Authentication
│   │   └── Database.php   # MySQL connection
│   ├── config/            # Configuration
│   ├── database/
│   │   └── schema.sql     # MySQL schema
│   ├── public/
│   │   ├── index.php      # API entry point
│   │   └── .htaccess      # URL rewriting
│   ├── .env               # Backend config
│   └── README.md          # Detailed API docs
├── src/                   # React frontend (unchanged)
├── public/                # Frontend assets (unchanged)
└── .env                   # Frontend config (updated)
```

## Quick Start

### Local Development

1. **Setup Database**
```bash
mysql -u root -p
CREATE DATABASE data_annotation;
USE data_annotation;
SOURCE api/database/schema.sql;
```

2. **Configure Backend**
```bash
cd api
cp .env.example .env
# Edit .env with your database credentials
```

3. **Start PHP Server**
```bash
cd api/public
php -S localhost:8000
```

4. **Start Frontend**
```bash
npm run dev
```

### cPanel Deployment

1. **Upload `api/` folder** to `public_html/api/`

2. **Create MySQL database** in cPanel:
   - Go to MySQL Databases
   - Create new database
   - Create user and assign to database
   - Import `api/database/schema.sql` via phpMyAdmin

3. **Configure `api/.env`**:
```env
DB_HOST=localhost
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password
JWT_SECRET=random-32-character-string
CORS_ALLOWED_ORIGINS=https://yourdomain.com
```

4. **Build and upload frontend**:
```bash
npm run build
# Upload dist/ contents to public_html/
```

5. **Update frontend config** (`.env.production`):
```env
VITE_API_URL=https://yourdomain.com/api
```

## Default Login

After database import:
- Email: `admin@example.com`
- Password: `admin123`

**Change this immediately in production!**

## API Endpoints

All endpoints remain the same as before:

### Auth
- POST `/api/auth/signup`
- POST `/api/auth/signin`
- POST `/api/auth/signout`
- GET `/api/auth/user`

### Tasks
- GET `/api/tasks`
- POST `/api/tasks/:id/submit`
- GET `/api/tasks/stats`

### Admin
- GET `/api/admin/stats`
- GET `/api/admin/users`
- PUT `/api/admin/users/:id/role`
- DELETE `/api/admin/users/:id`
- GET `/api/admin/products`
- POST `/api/admin/products`
- PUT `/api/admin/products/:id`
- DELETE `/api/admin/products/:id`
- POST `/api/admin/tasks/generate`
- GET `/api/admin/payment-gateways`
- POST `/api/admin/payment-gateways`
- PUT `/api/admin/payment-gateways/:id`
- DELETE `/api/admin/payment-gateways/:id`

## Database Schema

The MySQL schema includes these tables:
- `users` - User accounts
- `user_profiles` - User statistics
- `wallets` - User balances
- `earnings` - Transaction history
- `products` - Product catalog
- `brand_identification_tasks` - Task assignments
- `task_submissions` - Submitted work
- `payment_methods` - User payment info
- `payment_gateways` - Admin payment config

## Security Checklist

- [ ] Change JWT_SECRET to random string
- [ ] Change default admin password
- [ ] Set APP_DEBUG=false in production
- [ ] Update CORS_ALLOWED_ORIGINS to your domain
- [ ] Use HTTPS with valid SSL certificate
- [ ] Regular database backups
- [ ] Keep PHP updated (8.0+)

## Troubleshooting

### Database Connection Error
- Check `.env` credentials
- Verify MySQL is running
- Test: `mysql -u username -p database`

### 500 Server Error
- Enable `APP_DEBUG=true` in `.env`
- Check PHP error logs
- Verify `.htaccess` is uploaded
- Check mod_rewrite is enabled

### CORS Errors
- Update `CORS_ALLOWED_ORIGINS` in `.env`
- Check browser console for details
- Verify `.htaccess` headers

### 404 Not Found
- Check `.htaccess` files exist
- Verify mod_rewrite enabled
- Ensure API URL is correct

## Performance Tips

1. **Enable OpCache** in PHP for better performance
2. **Use connection pooling** for database
3. **Add indexes** to frequently queried columns
4. **Enable GZIP compression** in Apache
5. **Use CDN** for static assets
6. **Implement rate limiting** for API endpoints

## Backup Strategy

1. **Database**: Daily automated MySQL dumps
2. **Files**: Regular backup of `api/` folder
3. **Logs**: Archive `storage/logs/` weekly
4. **Config**: Keep `.env` in secure location

## Monitoring

Monitor these metrics:
- Database connection pool
- API response times
- Error logs
- User registration trends
- Task completion rates
- Payment processing

## Support

Refer to `api/README.md` for detailed API documentation and additional troubleshooting steps.
