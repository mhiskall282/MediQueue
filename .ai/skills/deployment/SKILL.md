---
name: deployment
description: Production server deployment, environment configuration, database migration execution, and asset building for MediQueue.
---

# Deployment Skill Guide

Use this skill when preparing, testing, or executing production deployments for **MediQueue**.

---

## 1. Pre-Deployment Verification Checklist

Before deploying MediQueue to staging or production:

1. [ ] Automated test suite passes (`php artisan test`).
2. [ ] Production environment file (`.env.production`) verified (no debug mode, app key set).
3. [ ] Node assets compiled using production minification (`npm run build`).
4. [ ] Database migrations tested (`php artisan migrate:fresh --seed`).
5. [ ] Route and view caches generated (`php artisan route:cache`, `php artisan view:cache`).

---

## 2. Deployment Script Command Sequence

```bash
# 1. Fetch latest changes
git pull origin main

# 2. Install PHP production dependencies
composer install --no-dev --optimize-autoloader

# 3. Build frontend assets
npm ci
npm run build

# 4. Run database migrations
php artisan migrate --force

# 5. Clear and rebuild application caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Verify health check endpoint
curl -s http://localhost/health | grep '"status":"healthy"'
```
