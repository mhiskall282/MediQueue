# MediQueue — Production Deployment Guide

**Document ID**: DEP-001  
**Version**: 1.0  
**Date**: 2026-08-14  
**Target Environments**: Docker (Local/Self-hosted) & Render.com (Cloud PaaS)  

---

## 1. Overview & Architecture

MediQueue is packaged as a self-contained container with PHP 8.2-FPM and Nginx managed by Supervisor. It uses a SQLite embedded database with file sessions and pre-compiled Vite frontend assets, making it exceptionally lightweight and zero-maintenance on free cloud tiers.

```
[ Internet / HTTPS ]
         │
         ▼
[ Render.com Edge / Load Balancer ]
         │
         ▼ (Port 80 / 8000)
[ Docker Container (Supervisor) ]
    ├─► [ Nginx Web Server ] ──► (Port 9000) ──► [ PHP 8.2-FPM ]
    └─► [ SQLite Database & File Storage ]
```

---

## 2. Deploying on Render.com

### Method 1: Blueprint Deployment with Managed PostgreSQL (Recommended)

1. Push this repository to your GitHub account:
   ```bash
   git push origin main
   ```
2. Log in to [Render Dashboard](https://dashboard.render.com).
3. Click **New +** → **Blueprint**.
4. Connect your GitHub repository (`ug-swe-exams`).
5. Render will automatically detect `render.yaml` and provision both:
   - **Managed PostgreSQL Database** (`mediqueue-db`): Dedicated PostgreSQL instance with internal network access.
   - **Web Service** (`mediqueue`): Dockerized Laravel app automatically connected to the database.
6. Click **Apply**.
7. The build pipeline will automatically compile Vite assets, install PHP extensions (`pdo_pgsql`), run database migrations, and seed initial demo accounts!

### Method 2: Manual Web Service Setup

1. Click **New +** → **PostgreSQL** to create a database (`mediqueue-db`).
2. Click **New +** → **Web Service** and connect your repository.
3. Select **Docker** as the Runtime environment.
4. Link the database environment variables in Render's dashboard:
   - `APP_NAME`: `MediQueue`
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: (Generate using `php artisan key:generate --show` or Render secret generator)
   - `APP_URL`: `https://<your-service-name>.onrender.com`
   - `DB_CONNECTION`: `pgsql`
   - `DB_HOST`: (From PostgreSQL internal database host)
   - `DB_PORT`: `5432`
   - `DB_DATABASE`: `mediqueue`
   - `DB_USERNAME`: `mediqueue_user`
   - `DB_PASSWORD`: (From PostgreSQL password)
   - `SESSION_DRIVER`: `file`
   - `CACHE_STORE`: `file`
   - `QUEUE_CONNECTION`: `sync`

---

## 3. Deploying with Docker Compose (Local / VPS)

### Prerequisites
- Docker Engine 24.0+
- Docker Compose v2.0+

### Steps

1. Clone repository:
   ```bash
   git clone <repo-url> mediqueue
   cd mediqueue
   ```

2. Build and start the container:
   ```bash
   docker compose up -d --build
   ```

3. Access the application:
   - Open browser at `http://localhost:8000`

4. View running container logs:
   ```bash
   docker compose logs -f app
   ```

5. Stop the container:
   ```bash
   docker compose down
   ```

---

## 4. Default Seeded Credentials for Testing

| Role | Email | Password | Purpose |
|---|---|---|---|
| **Administrator** | `admin@mediqueue.test` | `password` | System configuration, service creation, audit inspection |
| **Doctor / Staff** | `dr.sarah@mediqueue.test` | `password` | Calling patients, starting/completing consultations |
| **Nurse / Staff** | `nurse.james@mediqueue.test` | `password` | Nursing queue management |
| **Patient** | `john.doe@example.com` | `password` | Patient queue tickets, status monitoring |
| **Patient** | `jane.smith@example.com` | `password` | Active queue demonstration ticket |

---

## 5. Production Health Check & Verification

MediQueue exposes the standard Laravel health route at `/up`:
- **Endpoint**: `GET /up`
- **Response**: HTTP 200 OK
- Used by Render.com health checks to determine container readiness.
