# Project Context: MediQueue

## 1. Product Overview

**MediQueue — Smart Clinic Queue Management System**

MediQueue is a web-based clinic queue management solution tailored for small-to-medium outpatient healthcare facilities. It streamlines patient registration, issues sequential electronic queue tickets, tracks real-time queue status, provides estimated waiting times, enables clinic staff to call and serve patients, and allows clinic administrators to configure healthcare services and system user accounts.

---

## 2. Target Environment & User Groups

1. **Patients / Walk-ins**:
   - Access public digital kiosk/web portal.
   - Select desired medical service (e.g., General Consultation, Vaccination, Triage, Pharmacy).
   - Register basic personal info (Name, Phone Number, Patient ID/NRIC/Passport).
   - Receive a digital queue ticket with a unique ticket number, service desk identifier, estimated wait time, and live queue position monitor link.

2. **Reception / Triage Desk**:
   - Check-in patients, reassign queues, issue manual tickets for non-tech-savvy walk-ins, and override queue priorities for emergency cases.

3. **Healthcare Staff (Doctors / Nurses / Technicians)**:
   - Access staff dashboard for assigned consultation rooms/services.
   - View live waiting queue, call next patient, mark patient as "In Consultation", "Completed", "No-Show", or "Transferred".
   - Pause queue intake during breaks.

4. **System Administrator**:
   - Manage clinic services (operating hours, active status, average processing time per patient).
   - Manage staff accounts and room assignments.
   - View real-time analytics (daily patient count, average wait time, service bottlenecks).

---

## 3. Scope Boundaries & Constraints

* **Project Duration**: 48-Hour Individual University Software Engineering Examination.
* **Architecture**: Monolithic Web Application using Laravel 10/11 + Blade + Tailwind CSS + Vanilla JS / Alpine.js.
* **Database**: Relational Database (SQLite for local testing/dev, MySQL/PostgreSQL ready for deployment).
* **Strict Non-Scope**: MediQueue is strictly an administrative queue-management application. It does **NOT** handle electronic medical records (EMR), prescription writing, clinical diagnostics, laboratory telemetry, or billing/payment gateway integrations.

---

## 4. Key Performance Indicators (KPIs)

* **Zero Ticket Collision**: Guaranteed atomic ticket generation even during concurrent patient check-ins.
* **Low Latency Status Updates**: Instant status reflects for patients and staff without page reloads.
* **Clean UI**: Professional, intuitive SaaS visual design suitable for real clinical display monitors and mobile screens.
