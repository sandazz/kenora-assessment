# Workshop Registration System

## Setup Instructions

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

## Seeded Logins

- **Admin**: `admin@example.com` / `password`
- **Manager**: `manager@example.com` / `password`
- **Staff**: `staff@example.com` / `password`

