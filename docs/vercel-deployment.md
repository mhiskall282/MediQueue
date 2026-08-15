# MediQueue — Vercel Deployment & Zero-Cost Guide

**Document ID**: DEP-002  
**Target Environment**: Vercel Serverless (Hobby Free Tier) + Neon / Supabase (Free PostgreSQL)  
**Cost Comparison**: **$0.00 / month** (vs $14–$21/mo on Render paid tiers)

---

## 1. Why Vercel instead of Render?

| Feature | Render (Standard) | Vercel + Neon (Free Tier) |
|---|---|---|
| **Web Service Cost** | $7.00 / month (Starter) | **$0.00** (Unlimited Serverless executions) |
| **PostgreSQL DB Cost** | $7.00 / month (or 30-day trial) | **$0.00** (Neon Serverless Postgres: 0.5 GB free) |
| **SSL / HTTPS & CDN** | Included | Included with global Edge CDN |
| **Cold Starts / Inactivity** | Free tier sleeps after 15 min | Serverless functions scale instantly on-demand |
| **Total Monthly Cost** | **~$14.00 - $21.00/mo** | **$0.00 / mo (100% Free)** |

---

## 2. Serverless Architecture Overview

```
[ Internet / Patient Browser ]
              │
              ▼
    [ Vercel Global Edge CDN ]
         │               │
  (Static Assets)   (Dynamic Requests)
         │               │
         ▼               ▼
  [/public/build]   [api/index.php (Serverless PHP 8.2 Lambda)]
                         │
                         ▼
             [Neon / Supabase PostgreSQL] (Free Cloud DB)
```

1. **Static Files & Vite Assets** (`/build/*`, `/css/*`, `/js/*`, images) are cached and served directly by Vercel Edge CDN with zero serverless function overhead.
2. **Dynamic PHP Requests** execute on demand using the `@vercel-php` runtime via the `api/index.php` bridge.
3. **Storage & Views** are redirected to the writable `/tmp` filesystem partition.
4. **Database** connects to a free external cloud PostgreSQL (e.g. Neon.tech or Supabase).

---

## 3. Project Configuration Files

The following configuration files are already set up in the repository:

### `vercel.json`
Specifies the PHP 8.2 runtime, static asset caching headers, request routing, and serverless environment defaults.

### `api/index.php`
Serverless bridge that initializes required `/tmp` directories (`/tmp/views`, `/tmp/cache`, `/tmp/sessions`, `/tmp/logs`) before delegating to `public/index.php`.

### `.vercelignore`
Excludes dev dependencies, Docker artifacts, and testing caches to ensure fast deployment bundle sizes.

---

## 4. Step-by-Step Deployment Guide

### Step 1: Create a Free PostgreSQL Database
Because Vercel serverless functions are ephemeral, you need a persistent cloud database:

1. Go to **[Neon.tech](https://neon.tech)** (Recommended for Postgres) or **[Supabase.com](https://supabase.com)**.
2. Create a free account and a new project named `mediqueue-db`.
3. Copy the database connection details:
   - Host (e.g., `ep-quiet-snow-123456.us-east-2.aws.neon.tech`)
   - Database Name (e.g., `neondb` or `mediqueue`)
   - Username (e.g., `neondb_owner`)
   - Password
   - Port (Default: `5432`)

---

### Step 2: Push Repository to GitHub
Ensure all code and the new Vercel files are pushed to GitHub:
```bash
git add vercel.json api/index.php .vercelignore docs/vercel-deployment.md
git commit -m "feat: add Vercel serverless deployment setup"
git push origin main
```

---

### Step 3: Import Project into Vercel

1. Log in to your **[Vercel Dashboard](https://vercel.com/dashboard)**.
2. Click **Add New...** → **Project**.
3. Select and import your `ug-swe-exams` / `MediQueue` GitHub repository.
4. Keep the Framework Preset as **Other**.
5. **Build & Development Settings**:
   - **Build Command**: Leave as Default or `npm run build`
   - **Output Directory**: **Leave OFF / Default (DO NOT type `public`)** — `vercel.json` manages function and asset routing automatically.
   - **Install Command**: Leave as Default (`npm install`)

---

### Step 4: Configure Environment Variables in Vercel

Under **Environment Variables** in the Vercel project settings, add the following variables:

| Variable | Value / Notes |
|---|---|
| `APP_NAME` | `MediQueue` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | `base64:kCkNSwihlFVDA97qozR5yDW83gBJlkiYC0DOJ3UP7Aw=` *(or generate new with `php artisan key:generate --show`)* |
| `APP_URL` | `https://your-mediqueue-app.vercel.app` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `<your-neon-or-supabase-host>` |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `<your-neon-database-name>` |
| `DB_USERNAME` | `<your-neon-username>` |
| `DB_PASSWORD` | `<your-neon-password>` |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `cookie` *(recommended for serverless)* |
| `CACHE_STORE` | `array` |
| `QUEUE_CONNECTION` | `sync` |
| `LOG_CHANNEL` | `stderr` |
| `VIEW_COMPILED_PATH` | `/tmp/views` |

---

### Step 5: Run Database Migrations & Initial Data Seeding

Since you are deploying serverless and may not have PHP installed locally on Windows, MediQueue includes a secure built-in migration endpoint.

Once your Vercel deployment completes:
1. Open this URL in your web browser:
   ```
   https://<your-project-name>.vercel.app/setup/migrate?secret=<YOUR_APP_KEY>&seed=1
   ```
   *(Replace `<YOUR_APP_KEY>` with your `APP_KEY` environment variable, e.g. `base64:kCkNSwihlFVDA97qozR5yDW83gBJlkiYC0DOJ3UP7Aw=`)*

2. The endpoint will securely run all migrations (`php artisan migrate --force`) and seed default doctors/patients (`php artisan db:seed --force`) directly against your cloud database and display a success status screen in your browser!

---

### Step 6: Verify Live Site

1. Click **Deploy** in the Vercel dashboard.
2. Vercel will compile the Vite assets and deploy the serverless runtime.
3. Open your live app URL (e.g. `https://your-mediqueue.vercel.app`)!

---

## 5. Alternative: Deploying via Vercel CLI

If you prefer deploying from your terminal:

```bash
# 1. Install Vercel CLI globally
npm i -g vercel

# 2. Build local frontend assets
npm run build

# 3. Log in to Vercel
vercel login

# 4. Deploy to preview
vercel

# 5. Deploy to production
vercel --prod
```

---

## 6. Seeded Demo Accounts for Verification

After running `php artisan db:seed`, test your deployment with:

| Role | Email | Password |
|---|---|---|
| **Administrator** | `admin@mediqueue.test` | `password` |
| **Doctor / Staff** | `dr.sarah@mediqueue.test` | `password` |
| **Nurse / Staff** | `nurse.james@mediqueue.test` | `password` |
| **Patient** | `john.doe@example.com` | `password` |

---

## 7. Serverless Best Practices on Vercel

1. **Stateless Sessions**: Use `SESSION_DRIVER=cookie` (or `SESSION_DRIVER=database`) so user logins persist seamlessly across different serverless invocations.
2. **Writable Cache**: Laravel automatically compiles Blade templates to `/tmp/views` as configured in `vercel.json`.
3. **Database Connection Pooling**: If using Neon PostgreSQL, use Neon's pooled connection string (port 5432 or 6543) for high serverless concurrency.
