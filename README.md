# MediQueue — Smart Clinic Queue Management System

[![CI/CD Pipeline](https://github.com/mhiskall282/ug-swe-exams/actions/workflows/ci.yml/badge.svg)](https://github.com/mhiskall282/ug-swe-exams/actions)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=flat&logo=tailwind-css&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

> **Advanced Software Engineering Capstone Examination Project**  
> An enterprise-grade, accessible, responsive clinic queue management web application designed to eliminate physical waiting lines in healthcare outpatient clinics.

---

## 🌟 Key Features

### 👤 Patient Self-Service Portal
- **One-Click Queue Registration**: Select clinic service and receive an instant, atomic queue ticket (e.g. `GC-001`).
- **Live Status Monitoring**: Real-time position tracking (`#1 in line`, `People ahead: 0`), estimated wait times, and department status via lightweight asynchronous polling.
- **In-App Notifications**: Alerts when called, service started, completed, or skipped.
- **Queue History**: Review past clinic visits with timestamps and consultation wait durations.
- **Ticket Cancellation**: Option to cancel waiting tickets if plans change.

### 👨‍⚕️ Clinical Staff Operations Console
- **Focused Queue Control**: Single-click "Call Next Patient" honoring sequence numbers and urgent triage priorities.
- **Consultation State Workflow**: Seamless progression through `WAITING` → `CALLED` → `IN_SERVICE` → `COMPLETED`.
- **Handling Edge Cases**: "Skip" no-show patients and "Recall" them back into the active queue.
- **Live Department Analytics**: Waiting patient counts, completed volume today, and average wait time calculations.

### 🔧 System Administration & Governance
- **Clinic Service Catalogue**: Configure departments, ticket prefixes (e.g. `GC`, `LAB`, `PH`), and estimated duration per patient without downtime.
- **User & Role Management**: Create staff accounts, modify permissions (`patient`, `staff`, `admin`), and activate/deactivate accounts.
- **Immutable Audit Trail**: Append-only, time-stamped log recording every administrative and operational action with actor, IP, and metadata context.

---

## 🏗️ Architecture & Technology Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 12 (PHP 8.2+) |
| **Frontend UI** | Laravel Blade Components + Tailwind CSS v4 |
| **Asset Pipeline** | Vite 7 (pre-compiled production bundles) |
| **Database** | Normalized SQLite / MySQL Relational Schema |
| **State Machine** | Transaction-Safe `QueueService` with Pessimistic Locking |
| **Testing** | PHPUnit 11 / Pest PHP (Feature + Unit test suites) |
| **Containerization** | Multi-stage Docker (PHP 8.2-FPM + Nginx + Supervisor) |
| **Cloud Deployment** | Render.com Blueprint (`render.yaml`) |

---

## 🚀 Quick Start (Local Development)

### 1. Requirements
- PHP 8.2 or higher (with `pdo_sqlite`, `mbstring`, `openssl` extensions)
- Composer 2.x
- Node.js 20+ & npm

### 2. Installation & Setup

```bash
# 1. Clone repository
git clone https://github.com/mhiskall282/ug-swe-exams.git
cd ug-swe-exams

# 2. Install PHP & Node dependencies
composer install
npm install

# 3. Compile frontend assets
npm run build

# 4. Initialize environment & database
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# 5. Start local development server
php artisan serve
```

Access the application in your browser at: **`http://localhost:8000`**

---

## 🔑 Demo Seeded Credentials

| Role | Email | Password | Access Level |
|---|---|---|---|
| **Administrator** | `admin@mediqueue.test` | `password` | Full system control, services, users, audit |
| **Doctor / Staff** | `dr.sarah@mediqueue.test` | `password` | General consultation queue management |
| **Nurse / Staff** | `nurse.james@mediqueue.test` | `password` | Nursing department queue operations |
| **Patient** | `john.doe@example.com` | `password` | Patient queue tickets and history |
| **Patient** | `jane.smith@example.com` | `password` | Active queue ticket demonstration |

---

## 🧪 Automated Testing

Execute the complete automated test suite (34 tests, 101 assertions covering Auth, RBAC, Queue Lifecycle, Service Management, and Unit Calculations):

```bash
php vendor/bin/phpunit
```

---

## 🚢 Docker & Render.com Cloud Deployment

### Run with Docker Compose
```bash
docker compose up -d --build
```
Access at `http://localhost:8000`.

### Deploy to Render.com
1. Connect this GitHub repository on Render.
2. Select **Blueprint** deployment — Render will automatically read `render.yaml`.
3. Click **Apply** to launch your live instance.

---

## 📚 Examination & Software Engineering Documentation

Comprehensive engineering artifacts and documentation are available in `docs/`:

- 📋 [**Software Requirements Specification (SRS)**](docs/SRS.md) — Full functional and non-functional requirements with MoSCoW prioritization and Traceability Matrix.
- 📐 [**System Analysis & Design (SAD)**](docs/system-design.md) — Architectural diagrams, Entity-Relationship (ERD), Use Case diagrams, Sequence diagrams, and State Machine specifications.
- ⏱️ [**Software Effort Estimation**](docs/estimation/estimation.md) — Rigorous Use Case Points (UCP) estimation calculation performed prior to implementation.
- 🚢 [**Production Deployment Guide**](docs/deployment.md) — Comprehensive Docker, Nginx, and Render cloud deployment architecture.
- 🤖 [**AI Agent Environment Guide**](.ai/README.md) — The 49-file `.ai/` agentic development environment and engineering rules.

---

## 📄 License
Open source under the [MIT License](LICENSE).
