# MediQueue — Smart Hospital Queue & Clinical Care Platform

[![Live Production Vercel](https://img.shields.io/badge/Live_Production-Vercel-000000?style=for-the-badge&logo=vercel&logoColor=white)](https://medi-queue-pmdd.vercel.app)
[![Live Production Render](https://img.shields.io/badge/Live_Production-Render.com-00c7b7?style=for-the-badge&logo=render&logoColor=white)](https://mediqueue-25vl.onrender.com)
[![GitHub Repository](https://img.shields.io/badge/GitHub-MediQueue-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/mhiskall282/MediQueue)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Automated Tests](https://img.shields.io/badge/Tests-68%20Passed%20(276%20Assertions)-brightgreen.svg)](https://github.com/mhiskall282/MediQueue)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> **🏥 University of Ghana Medical Centre (UGMC)** — Smart Clinic Telemetry & Queue Care Platform  
> **⚡ Live Vercel Production (Zero-Cost)**: **[https://medi-queue-pmdd.vercel.app](https://medi-queue-pmdd.vercel.app)**  
> **🌐 Live Render Production**: **[https://mediqueue-25vl.onrender.com](https://mediqueue-25vl.onrender.com)**  
> **📺 Public TV Waiting Display**: **[https://medi-queue-pmdd.vercel.app/display](https://medi-queue-pmdd.vercel.app/display)**  
> **📚 In-App Documentation Hub**: **[https://medi-queue-pmdd.vercel.app/docs](https://medi-queue-pmdd.vercel.app/docs)**  
> **📑 Final Capstone Examination Report**: [`MediQueue_Capstone_Final_Report.pdf`](MediQueue_Capstone_Final_Report.pdf)
>
> **Advanced Software Engineering Capstone Examination Project**  
> An enterprise-grade, accessible, responsive smart hospital queue management and clinical operations platform designed to eliminate physical waiting lines, streamline emergency triage, manage ward beds, facilitate diagnostic lab referral loops, and maintain HIPAA / ISO-27001 compliance for outpatient healthcare facilities.

---

## 🗺️ Complete System Routes & URL Directory

Below is the exhaustive index of all HTTP routes, interfaces, and operational endpoints available across the platform:

### 1. 🌐 Public & Shared Hospital Screens
| Method | URL Path | Route Name | Description | Access Level |
|---|---|---|---|---|
| `GET` | `/` | `home` | Modern branded landing page with quick evaluation sandbox accounts | Public |
| `GET` | `/display` | `display` | Public TV Departure Screen with Web Audio chime (*Ding-Dong*) & status board | Public / Waiting Lounge |
| `GET` | `/display/data` | `display.data` | Real-time JSON telemetry stream for TV screen auto-refresh | Public / Kiosk API |
| `GET` | `/docs` | `docs` | Interactive in-app technical & non-technical documentation hub | Public |
| `GET` | `/login` | `login` | Authentication sign-in portal with rate limiting (10 req/min) | Guest |
| `POST` | `/login` | `login.store` | Authenticate session and check mandatory password change | Guest |
| `GET` | `/register` | `register` | Outpatient & Medical Staff registration with licensing inputs | Guest |
| `POST` | `/register` | `register.store` | Process registration and queue staff accounts for admin approval | Guest |
| `GET/POST`| `/logout` | `logout` | Safe dual-method session termination and audit logging | Authenticated |

---

### 2. 👤 Outpatient Self-Service Portal (`/patient/*`)
| Method | URL Path | Route Name | Description | Access Level |
|---|---|---|---|---|
| `GET` | `/patient/dashboard` | `patient.dashboard` | Patient home dashboard with active ticket and notifications | Patient |
| `GET` | `/patient/queue` | `patient.queue.index` | Department catalogue with wait times and queue lengths | Patient |
| `GET` | `/patient/queue/service/{id}` | `patient.queue.show` | Service detail page before taking a ticket | Patient |
| `POST` | `/patient/queue` | `patient.queue.store` | Generate atomic queue ticket (e.g. `GC-005`) | Patient |
| `GET` | `/patient/queue/{id}/status` | `patient.queue.status` | Real-time queue position monitor (auto-refreshes every 4s) | Patient |
| `GET` | `/patient/queue/{id}/status.json` | `patient.queue.status.json` | JSON polling endpoint for live queue updates | Patient |
| `POST` | `/patient/queue/{id}/cancel` | `patient.queue.cancel` | Cancel active waiting ticket | Patient |
| `GET` | `/patient/history` | `patient.history` | Personal clinic consultation history with duration logs | Patient |
| `GET` | `/patient/appointments` | `patient.appointments.index`| View upcoming booked doctor clinic appointments | Patient |
| `GET` | `/patient/appointments/create` | `patient.appointments.create`| Book advance doctor consultation appointment | Patient |
| `POST` | `/patient/appointments` | `patient.appointments.store`| Save advance appointment and send email confirmation | Patient |

---

### 3. 🩺 Clinical Staff Operations Console (`/staff/*`)
| Method | URL Path | Route Name | Description | Access Level |
|---|---|---|---|---|
| `GET` | `/staff/dashboard` | `staff.dashboard` | Multi-department clinical queue console & active consultation desk | Doctor / Nurse / Staff / Admin |
| `POST` | `/staff/queue/call-next` | `staff.queue.call-next` | Call next patient prioritized by 5-tier triage acuity | Doctor / Nurse / Staff / Admin |
| `POST` | `/staff/queue/{id}/start` | `staff.queue.start` | Begin consultation timer (`IN_SERVICE`) | Clinicians |
| `POST` | `/staff/queue/{id}/complete` | `staff.queue.complete` | Conclude service, release bed, dispatch summary email | Clinicians |
| `POST` | `/staff/queue/{id}/skip` | `staff.queue.skip` | Mark no-show patient as skipped | Clinicians |
| `POST` | `/staff/queue/{id}/recall` | `staff.queue.recall` | Recall skipped patient back into active consultation | Clinicians |
| `POST` | `/staff/queue/{id}/triage` | `staff.queue.triage` | Update 5-tier Manchester triage level (🔴 P1 to 🔵 P5) | Nurse / Doctor / Admin |
| `GET` | `/staff/beds` | `staff.beds.index` | Ward, Bay & Resuscitation Bed management console | Nurse / Doctor / Admin |
| `POST` | `/staff/queue/{id}/allocate-bed` | `staff.queue.allocate-bed`| Allocate hospital bed/bay to an active patient ticket | Nurse / Doctor / Admin |
| `POST` | `/staff/queue/{id}/release-bed` | `staff.queue.release-bed` | Manually release occupied hospital bed | Nurse / Doctor / Admin |
| `GET` | `/staff/emergency` | `staff.emergency.index` | 🚨 Emergency Trauma & Unconscious Patient Admission Protocol | Clinicians |
| `POST` | `/staff/emergency/unconscious-intake` | `staff.emergency.unconscious-intake`| Rapid Code Red intake with temporary MRN & Bed allocation | Clinicians |
| `POST` | `/staff/emergency/{id}/link-permanent-id` | `staff.emergency.link-permanent-id` | Merge trauma ticket into patient permanent hospital ID | Clinicians |
| `POST` | `/referral/{id}/order-lab` | `staff.referral.order-lab` | Order diagnostic lab investigations and transfer ticket | Medical Doctors |
| `POST` | `/referral/{id}/complete-lab` | `staff.referral.complete-lab` | Submit lab test findings and return patient to doctor loop | Lab Technologists |
| `POST` | `/referral/{id}/discharge` | `staff.referral.discharge` | Write final discharge care summary, prescriptions & dispatch | Medical Doctors |
| `GET` | `/staff/oncall` | `staff.oncall.index` | Doctor On-Call Roster and Active Medical Staff Board | Clinicians |
| `GET` | `/staff/messages` | `staff.messages.index` | Inter-staff clinical messaging, consults & STAT emergency notes | Clinicians |
| `POST` | `/staff/messages` | `staff.messages.store` | Dispatch secure clinical note with patient ticket attachment | Clinicians |
| `GET` | `/staff/onboarding` | `staff.onboarding` | Medical staff self-onboarding & practicing license submission | Staff |
| `PUT` | `/staff/onboarding` | `staff.onboarding.update` | Submit practicing credentials for admin approval | Staff |

---

### 4. 👑 System Administration & Security Center (`/admin/*`)
| Method | URL Path | Route Name | Description | Access Level |
|---|---|---|---|---|
| `GET` | `/admin/dashboard` | `admin.dashboard` | Executive hospital dashboard with real-time clinic KPIs | Administrator |
| `GET` | `/admin/security-alerts` | `admin.security.index` | 🛡️ HIPAA & ISO 27001 Security Center & Anomaly Telemetry | Administrator |
| `POST` | `/admin/security-alerts/{id}/resolve` | `admin.security.resolve` | Resolve security incident with compliance mitigation notes | Administrator |
| `GET` | `/admin/reports` | `admin.reports.index` | Clinical analytics, attendance reports, and PDF/CSV exports | Administrator |
| `GET` | `/admin/reports/export/csv` | `admin.reports.export.csv` | Export streaming CSV audit dataset | Administrator |
| `GET` | `/admin/reports/export/pdf` | `admin.reports.export.pdf` | Generate styled PDF clinical operational summary report | Administrator |
| `POST` | `/admin/reports/send-email` | `admin.reports.send-email` | Dispatch operational summary report directly to executive email | Administrator |
| `GET` | `/admin/users` | `admin.users.index` | User management portal with **Pending Staff Approvals** tab | Administrator |
| `POST` | `/admin/users` | `admin.users.store` | Provision user account with role assignment | Administrator |
| `GET` | `/admin/users/{id}/edit` | `admin.users.edit` | Edit user profile and **Dynamic Privilege Extension Matrix** | Administrator |
| `PUT` | `/admin/users/{id}` | `admin.users.update` | Save user profile and granular capability overrides | Administrator |
| `POST` | `/admin/users/{id}/approve` | `admin.users.approve` | Verify medical license and approve staff account | Administrator |
| `POST` | `/admin/users/{id}/revoke` | `admin.users.revoke` | Instantly revoke user or staff access | Administrator |
| `POST` | `/admin/users/{id}/reset-password` | `admin.users.reset-password`| Reset user password and dispatch credentials via email | Administrator |
| `GET` | `/admin/services` | `admin.services.index` | Clinic departments and service catalogue configuration | Administrator |
| `GET` | `/admin/audit-logs` | `admin.audit.index` | Append-only immutable forensic audit trail | Administrator |
| `GET` | `/admin/settings` | `admin.settings.index` | Hospital facility details, operating hours, and email rules | Administrator |

---

### 5. ⚙️ Personal Account & Security Settings (`/settings/*`)
| Method | URL Path | Route Name | Description | Access Level |
|---|---|---|---|---|
| `GET` | `/settings` | `settings.index` | Personal profile info, notification preferences & sign-in telemetry | All Roles |
| `PUT` | `/settings/profile` | `settings.profile` | Update contact numbers and email notification preferences | All Roles |
| `PUT` | `/settings/password` | `settings.password` | Update account password with current password verification | All Roles |
| `GET` | `/force-password-change` | `password.force-change` | Mandatory first-time password change gate for admin-created users | Authenticated |
| `POST` | `/force-password-change` | `password.force-change.update`| Set private password and activate account | Authenticated |

---

## 🌟 Key Platform Features

### 📺 Public Departure Hall TV Screen (`/display`)
- **Hospital Departure-Board Display**: Dedicated TV waiting room screen featuring high-contrast "Now Calling" ticket numbers with animated glow, department status matrix, live clock, and automated 3-second data synchronization.
- **Web Audio API Audible Chime**: Synthesized *Ding-Dong* audio tone that chimes automatically when any clinician calls a new patient.

### 👤 Outpatient Self-Service Portal
- **One-Click Queue Registration**: Select clinic service and receive an instant, atomic queue ticket (e.g. `GC-001`).
- **Live Status Monitoring**: Real-time position tracking (`#1 in line`, `People ahead: 0`), estimated wait times, and department status via lightweight asynchronous polling.
- **In-App & Email Notifications**: Automated transactional emails and in-app alerts when tickets are issued, called, in-service, completed, or cancelled.
- **Queue History**: Review past clinic visits with timestamps and consultation wait durations.

### 🩺 Clinical Staff Operations Console
- **Focused Queue Control**: Single-click "Call Next Patient" honoring sequence numbers and urgent triage priorities.
- **Consultation State Workflow**: Seamless progression through `WAITING` → `CALLED` → `IN_SERVICE` → `COMPLETED`.
- **Handling Edge Cases**: "Skip" no-show patients and "Recall" them back into active consultation.
- **5-Tier Manchester Emergency Triage**: Acuity scoring (🔴 P1 Immediate Resuscitation, 🟠 P2 Very Urgent, 🟡 P3 Priority, 🟢 P4 Standard, 🔵 P5 Routine).
- **Ward & Resuscitation Bed Allocations**: Real-time bed tracking (AVAILABLE, OCCUPIED, CLEANING, MAINTENANCE) with automatic release upon clinical discharge.
- **Emergency Trauma Rapid Admission Protocol**: Anonymous intake for unconscious casualties with temporary MRN generation (`EMG-DOE-7821`) and automated on-call doctor paging.
- **Dual-Loop Diagnostic Laboratory Loop**: Doctor lab ordering, specimen processing, findings entry, and automated loopback to doctor review.

### 👑 System Administration & Security Governance
- **Medical Staff Licensing Gate & Approvals**: Review applicant practicing licenses (`MDC-GH`, `NMC`, `PC`) with one-click verify and access revocation.
- **Dynamic Privilege Extension Matrix**: Admin-controlled granular capability overrides (`can_consult`, `can_triage`, `can_execute_lab`, `can_assign_beds`).
- **HIPAA & ISO-27001 Security Incident Center**: Real-time brute-force rate-limiting, login IP change anomaly detection, and one-click incident resolution.
- **Reporting & Analytics Desk**: Attendance KPIs, streaming CSV data exports, and formatted PDF clinical summaries.
- **Immutable Forensic Audit Trail**: Append-only, time-stamped log recording every administrative and operational action with actor, IP, and metadata context.

---

## 🏗️ Architecture & Technology Stack

| Layer | Technology | Specification / Details |
|---|---|---|
| **Backend Framework** | Laravel 12 (PHP 8.2+) | Modern MVC, Form Requests, Eloquent ORM, Middleware Pipelines |
| **Frontend UI** | Blade Components + Tailwind CSS v4 | Curated Slate/Indigo/Emerald palettes, OKLCH tokens, responsive layout |
| **Asset Pipeline** | Vite 7 | Pre-compiled client bundle (`public/build/`) |
| **Database** | Relational DB (PostgreSQL / SQLite) | Fully normalized third-normal-form (3NF) relational schema |
| **State Machine** | Transaction-Safe `QueueService` | Atomic ticketing with pessimistic row locking (`lockForUpdate`) |
| **Testing** | PHPUnit 11 | 68 Automated Feature & Unit Tests, 276 Assertions (100% Pass) |
| **Containerization** | Multi-stage Docker | PHP 8.2-FPM + Nginx + Supervisor on Alpine Linux |
| **Cloud Deployment** | Render.com Blueprint (`render.yaml`) | Automated deployment with Managed PostgreSQL database instance |

---

## 📋 Software Requirements Specification (SRS) Summary

### Functional Requirements Coverage
- **FR-AUTH**: Authentication, granular clinical RBAC, mandatory first-time password reset, and self-onboarding.
- **FR-QUE**: Atomic ticket generation, FIFO and Manchester acuity prioritized queue calling, state transitions.
- **FR-TRIAGE**: 5-Tier Manchester Emergency Triage acuity assessment with priority queue elevation.
- **FR-BED**: Hospital ward, bay, and resuscitation bed allocation and automatic release on discharge.
- **FR-LAB**: Diagnostic laboratory referral orders, specimen processing, and automated physician review loop.
- **FR-EMG**: Anonymous rapid trauma admission protocol for unconscious casualties with temporary MRN generation.
- **FR-SEC**: HIPAA and ISO-27001 security telemetry, brute-force rate-limiting, login IP tracking, and audit logging.
- **FR-NOT**: Multi-channel transactional notifications via Zoho Mail SMTP with UGMC hospital branding.

---

## ⏱️ Software Effort Estimation (Use Case Points — UCP)

Calculated prior to implementation using Gustav Karner's algorithmic Use Case Points formula:

$$\text{UCP} = (\text{UAW} + \text{UUCW}) \times \text{TCF} \times \text{ECF}$$

| Metric | Value | Details |
|---|---|---|
| **Unadjusted Actor Weight (UAW)** | **14** | 1 Simple, 2 Average, 3 Complex Actors |
| **Unadjusted Use Case Weight (UUCW)** | **150** | 14 Core Use Cases (4 Simple, 6 Average, 4 Complex) |
| **Unadjusted Use Case Points (UUCP)** | **164** | $\text{UAW} + \text{UUCW}$ |
| **Technical Complexity Factor (TCF)** | **0.935** | 13 Technical Factors assessed ($T_1 \dots T_{13}$) |
| **Environmental Complexity Factor (ECF)**| **0.590** | 8 Environmental Factors assessed ($E_1 \dots E_8$) |
| **Adjusted Use Case Points (UCP)** | **~90 UCP** | Fits comfortably within individual 48-hour examination scope |

---

## 🔑 Demo Seeded Evaluation Credentials

| Role | Name | Email | Password | Assigned Hospital ID |
|---|---|---|---|---|
| **Hospital Administrator** | Dr. Kwame Mensah | `admin@mediqueue.test` | `password` | `MED-ADM-00001` |
| **Medical Doctor** | Dr. Sarah Ahmad | `dr.sarah@mediqueue.test` | `password` | `MED-DOC-00101` |
| **Staff Nurse / Triage** | Nurse James Wilson | `nurse.james@mediqueue.test` | `password` | `MED-NUR-00201` |
| **Clinical Pharmacist** | Pharm. Linda Osei | `pharm.linda@mediqueue.test` | `password` | `MED-PHM-00301` |
| **Lab Technologist** | Lab Tech Samuel Koffi| `lab.samuel@mediqueue.test` | `password` | `MED-LAB-00401` |
| **Front Desk / Reception**| Grace Addo | `reception.grace@mediqueue.test`| `password` | `MED-STF-00501` |
| **Registered Patient** | John Doe | `john.doe@example.com` | `password` | `MRN-2026-10001` |

---

## 🚀 Quick Start (Local Development)

```bash
# 1. Clone repository
git clone https://github.com/mhiskall282/MediQueue.git
cd MediQueue

# 2. Install dependencies
composer install
npm install

# 3. Build frontend assets
npm run build

# 4. Configure environment & database
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# 5. Start development server
php artisan serve
```

Access in your browser at: **`http://localhost:8000`**

---

## 🧪 Automated Testing (100% Pass)

Execute the complete 68-test automated regression suite:

```bash
php vendor/bin/phpunit
```

```
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.2.12
Configuration: phpunit.xml

................................................................. 65 / 68 ( 95%)
...                                                               68 / 68 (100%)

Time: 00:13.389, Memory: 54.00 MB
OK (68 tests, 276 assertions)
```

---

## 🚢 Docker & Cloud Deployment

### Docker Compose Local Execution
```bash
docker compose up -d --build
```

### Vercel Serverless (Zero-Cost / Free Tier)
1. Import repository to **[Vercel Dashboard](https://vercel.com)**.
2. Set Build Command: `npm run build` and Output Directory: `public`.
3. Link free PostgreSQL database (e.g., [Neon.tech](https://neon.tech) / [Supabase](https://supabase.com)) and set environment variables.
4. Full guide available at: [**Vercel Serverless Deployment Guide**](docs/vercel-deployment.md).

---

## 📚 Examination & Software Engineering Documentation

Comprehensive engineering artifacts and documentation are available in `docs/`:

- 📋 [**Software Requirements Specification (SRS)**](docs/SRS.md) — Complete functional, non-functional, clinical, and security specifications.
- 📐 [**System Analysis & Design (SAD)**](docs/system-design.md) — Architectural diagrams, ERD, Use Case diagrams, and Sequence models.
- ⏱️ [**Software Effort Estimation**](docs/estimation/estimation.md) — Algorithmic Use Case Points (UCP) estimation calculation.
- 🚢 [**Render Production Deployment Guide**](docs/deployment.md) — Docker, Nginx, and Render cloud deployment architecture.
- ⚡ [**Vercel Zero-Cost Serverless Guide**](docs/vercel-deployment.md) — 100% Free deployment with Vercel and Neon PostgreSQL.
- 📑 [**Academic Capstone Final Report**](MediQueue_Capstone_Final_Report.pdf) — Complete academic examination capstone document.

---

## 📄 License
Open source under the [MIT License](LICENSE).
