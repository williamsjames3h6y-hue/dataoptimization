# Data Annotation Platform

A professional data annotation platform with Laravel-style PHP backend and React frontend.

## Features

- User authentication and wallet system
- Brand identification tasks with earnings
- Product management
- Admin dashboard with analytics
- Payment gateway integration
- MySQL database

## Project Structure

```
├── api/              # Backend (Laravel-style PHP)
├── dist/             # Built frontend (React)
├── public/           # Static assets
├── src/              # Frontend source (React + TypeScript)
└── .htaccess         # Apache configuration
```

## Local Development

1. Install dependencies:
```bash
npm install
cd api && composer install
```

2. Configure environment:
```bash
cp api/.env.example api/.env
# Edit api/.env with your database credentials
```

3. Setup database:
```bash
php api/database/setup.php
```

4. Build frontend:
```bash
npm run build
```

5. Start development:
```bash
npm run dev
```

## Deployment

See `DEPLOYMENT_SIMPLE.md` for Hostinger deployment instructions.

## Tech Stack

- **Frontend:** React, TypeScript, Tailwind CSS, Vite
- **Backend:** PHP, MySQL
- **Authentication:** JWT
- **Hosting:** Hostinger with Apache

## Live Site

https://earningsllc.online
