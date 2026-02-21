# Demo Instructions - Laravel + MySQL Backend

## Current Status

Your application has been successfully migrated from Supabase to Laravel + MySQL architecture!

### What's Running

A **mock API server** is currently running on `localhost:8000` to demonstrate the frontend without requiring MySQL setup.

The mock server simulates all API endpoints with sample data so you can see the application working.

## Try the App

### Login Credentials

- **Email**: `admin@example.com`
- **Password**: `admin123`

### What You Can Do

1. **View Landing Page** - See the beautiful homepage
2. **Login** - Authenticate with the mock backend
3. **Dashboard** - View user stats and tasks
4. **Admin Panel** - Manage users, products, and payment gateways
5. **Annotations** - Browse and complete brand identification tasks

## Full Production Setup

### For Local Development with Real MySQL

1. **Install MySQL** (if not already installed)

2. **Create Database**
```bash
mysql -u root -p
CREATE DATABASE data_annotation;
USE data_annotation;
SOURCE api/database/schema.sql;
```

3. **Configure Backend** (`api/.env`)
```env
DB_HOST=127.0.0.1
DB_DATABASE=data_annotation
DB_USERNAME=root
DB_PASSWORD=your_password
JWT_SECRET=random-32-character-string
```

4. **Start Real API Server**
```bash
cd api/public
php -S localhost:8000 index.php
```

5. **Start Frontend**
```bash
npm run dev
```

### For cPanel Hosting

Follow the detailed instructions in `LARAVEL_DEPLOYMENT.md`

## Key Features

### Backend (Laravel-like PHP)
- Pure PHP 8+ with no external dependencies
- PDO-based MySQL connection
- JWT authentication
- RESTful API architecture
- Works on any cPanel/shared hosting

### Frontend (React + TypeScript)
- Modern React with hooks
- TypeScript for type safety
- Tailwind CSS for styling
- Lucide React icons
- Fully responsive design

### Database (MySQL)
- Complete schema with all tables
- Foreign key relationships
- Sample data included
- Optimized indexes

## File Structure

```
project/
├── api/                          # Laravel-like backend
│   ├── app/
│   │   ├── Controllers/         # API logic
│   │   │   ├── AuthController.php
│   │   │   ├── TaskController.php
│   │   │   └── AdminController.php
│   │   ├── Middleware/
│   │   │   └── Auth.php         # JWT authentication
│   │   └── Database.php         # PDO MySQL wrapper
│   ├── config/
│   │   ├── app.php              # App configuration
│   │   └── database.php         # Database config
│   ├── database/
│   │   └── schema.sql           # MySQL schema + sample data
│   ├── public/
│   │   ├── index.php            # API entry point
│   │   ├── mock-server.php      # Demo mock server
│   │   └── .htaccess            # URL rewriting
│   ├── .env                     # Environment variables
│   └── README.md                # Detailed API docs
│
├── src/                         # React frontend
│   ├── components/              # UI components
│   ├── contexts/                # React context
│   ├── lib/
│   │   └── api.ts              # API client
│   └── App.tsx                  # Main app
│
└── dist/                        # Production build
```

## API Endpoints

### Authentication
- `POST /api/auth/signup` - Register new user
- `POST /api/auth/signin` - Login user
- `POST /api/auth/signout` - Logout user
- `GET /api/auth/user` - Get current user

### Tasks (User)
- `GET /api/tasks` - Get available tasks
- `POST /api/tasks/:id/submit` - Submit task
- `GET /api/tasks/stats` - Get user statistics

### Admin
- `GET /api/admin/stats` - Platform statistics
- `GET /api/admin/users` - List all users
- `PUT /api/admin/users/:id/role` - Update user role
- `DELETE /api/admin/users/:id` - Delete user
- `GET /api/admin/products` - List products
- `POST /api/admin/products` - Create product
- `PUT /api/admin/products/:id` - Update product
- `DELETE /api/admin/products/:id` - Delete product
- `POST /api/admin/tasks/generate` - Generate tasks
- `GET /api/admin/payment-gateways` - List gateways
- `POST /api/admin/payment-gateways` - Create gateway
- `PUT /api/admin/payment-gateways/:id` - Update gateway
- `DELETE /api/admin/payment-gateways/:id` - Delete gateway

## Database Tables

1. **users** - User accounts with auth
2. **user_profiles** - User stats and achievements
3. **wallets** - User wallet and balances
4. **earnings** - Transaction history
5. **products** - Product catalog for annotation
6. **brand_identification_tasks** - Task assignments
7. **task_submissions** - Submitted annotations
8. **payment_methods** - User payment preferences
9. **payment_gateways** - Admin payment configuration

## Technology Stack

### Backend
- PHP 8.0+
- PDO MySQL
- JWT Authentication
- RESTful API

### Frontend
- React 18
- TypeScript
- Vite
- Tailwind CSS
- Lucide React

### Database
- MySQL 5.7+

## Security Features

- JWT token-based authentication
- Password hashing with bcrypt
- SQL injection prevention via PDO prepared statements
- CORS configuration
- Role-based access control
- Input validation

## Next Steps

1. **Test the demo** with the mock server
2. **Setup MySQL** for local development
3. **Deploy to cPanel** following deployment guide
4. **Customize** branding and features
5. **Add** payment gateway integrations
6. **Implement** email notifications
7. **Scale** with database optimization

## Support

- Check `api/README.md` for API documentation
- See `LARAVEL_DEPLOYMENT.md` for deployment steps
- Review `api/database/schema.sql` for database structure

## Notes

- The mock server is for demonstration only
- Production requires MySQL database setup
- Change default admin password immediately
- Generate random JWT_SECRET for production
- Use HTTPS in production
- Enable database backups
