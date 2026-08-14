# Deployment & Production Context

## 1. Production Architecture Requirements

MediQueue is designed for deployment on Linux/Windows server environments running Nginx/Apache + PHP 8.2+ + SQLite/MySQL.

---

## 2. Environment Configuration Pipeline

### Mandatory Environment Variables (`.env`):
```ini
APP_NAME="MediQueue"
APP_ENV=production
APP_KEY=base64:GeneratedKeyHere...
APP_DEBUG=false
APP_URL=http://localhost:8000

LOG_CHANNEL=daily
LOG_LEVEL=info

DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

---

## 3. Production Deployment Build Steps

```bash
# 1. Install PHP dependencies (Optimized autoloader, no dev dependencies)
composer install --no-dev --optimize-autoloader

# 2. Install Node dependencies and compile production UI assets
npm ci
npm run build

# 3. Execute database migrations and seeders
php artisan migrate --force --seed

# 4. Cache framework configurations and routes for optimal performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Set appropriate filesystem permissions
chmod -R 775 storage bootstrap/cache
```

---

## 4. Health Check Verification

The application exposes a health check endpoint `/health` returning JSON:
```json
{
  "status": "healthy",
  "database": "connected",
  "timestamp": "2026-08-14T03:45:00Z"
}
```
Deployment scripts must ping `/health` to verify zero deployment downtime before switching traffic.
