# MediQueue — Smart Hospital Queue & Clinical Care Platform

[![Live Production](https://img.shields.io/badge/Live_Production-Render.com-00c7b7?style=for-the-badge&logo=render&logoColor=white)](https://mediqueue-25vl.onrender.com)
[![GitHub Repository](https://img.shields.io/badge/GitHub-MediQueue-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/mhiskall282/MediQueue)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Automated Tests](https://img.shields.io/badge/Tests-68%20Passed%20(276%20Assertions)-brightgreen.svg)](https://github.com/mhiskall282/MediQueue)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> **🏥 University of Ghana Medical Centre (UGMC)** — Smart Clinic Telemetry & Queue Care Platform  
> **🌐 Live Production Deployment**: **[https://mediqueue-25vl.onrender.com](https://mediqueue-25vl.onrender.com)**  
> **📺 Public TV Waiting Display**: **[https://mediqueue-25vl.onrender.com/display](https://mediqueue-25vl.onrender.com/display)**  
> **📚 In-App Documentation Hub**: **[https://mediqueue-25vl.onrender.com/docs](https://mediqueue-25vl.onrender.com/docs)**  
> **📑 Final Capstone Examination Report**: [`MediQueue_Capstone_Final_Report.pdf`](MediQueue_Capstone_Final_Report.pdf)

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

Time: 00:07.447, Memory: 54.00 MB
OK (68 tests, 276 assertions)
```

---

## 🚢 Docker & Production Cloud Deployment

### Docker Compose Local Deployment
```bash
docker compose up -d --build
```

### Render.com Cloud Deployment
1. Connect **[https://github.com/mhiskall282/MediQueue](https://github.com/mhiskall282/MediQueue)** on Render.
2. Select **Blueprint** (`render.yaml`).
3. Click **Apply** to automatically provision PostgreSQL and the web application.

---

## 📄 License
Open source under the [MIT License](LICENSE).
