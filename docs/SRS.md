# MediQueue — Software Requirements Specification (SRS)

**Document ID**: SRS-001  
**Version**: 2.0  
**Date**: 2026-08-14  
**Hospital Facility**: University of Ghana Medical Centre (UGMC), Legon, Accra  
**Project**: MediQueue — Smart Hospital Queue & Clinical Care Platform  

---

## 1. Introduction

### 1.1 Purpose
This Software Requirements Specification (SRS) defines the functional, behavioral, clinical, and non-functional security requirements for **MediQueue**, an intelligent enterprise smart hospital queue management and clinical operations system deployed at the **University of Ghana Medical Centre (UGMC)**.

### 1.2 Clinical & Operational Scope
- **Granular Clinical Roles & Least Privilege**: Strict separation between Medical Doctors, Staff Nurses, Laboratory Technologists, Clinical Pharmacists, Front Desk Receptionists, and Hospital Administrators.
- **Medical Staff Onboarding & Licensing Vetting Gate**: Mandatory medical license verification (`MDC-GH`, `NMC`, `PC`) and administrator credentialing approval prior to clinical data access.
- **5-Tier Manchester Acuity Triage**: Real-time acuity classification (🔴 P1 Immediate Resuscitation, 🟠 P2 Very Urgent, 🟡 P3 Urgent, 🟢 P4 Standard, 🔵 P5 Non-Urgent) with priority queue sorting.
- **Ward & Resuscitation Bed Management**: Real-time bed and bay allocation with release triggers upon clinical discharge.
- **Dual-Loop Diagnostic Laboratory Transfers**: Seamless physician lab ordering, diagnostic findings entry, and automated review loops.
- **Emergency Trauma Rapid Admission Protocol**: Anonymous intake protocol for unconscious trauma casualties with temporary MRN generation and automated Code Red pages.
- **HIPAA & ISO 27001 Security Incident Telemetry Center**: Real-time brute-force rate-limiting, login IP anomaly tracking, privilege escalation detection, and immutable forensic audit logging.
- **User Account Settings & Mandatory Password Gates**: Personal profile and notification management, forced first-time password changes, and live transactional email delivery via Zoho Mail SMTP.
- **Public Departure Hall Display**: Live polling waiting lounge monitor with Web Audio API chime (*Ding-Dong*) and scrolling emergency notices.

---

## 2. Granular Role & Permission Matrix (Principle of Least Privilege)

| Role Code | Role Name | Allowed System Capabilities | Restricted Actions |
|---|---|---|---|
| `doctor` | **Medical Doctor / Physician** | Medical Consultations, Diagnostic Lab Orders, Prescriptions, Discharges, Code Red Lead | Administrative User Management |
| `nurse` | **Staff Nurse / Triage Specialist** | 5-Tier Acuity Triage (P1-P5), Vital Signs, Ward Bed Allocations | Final Medical Discharges |
| `pharmacist` | **Clinical Pharmacist** | Pharmacy Queue Dispensing, Prescription Fulfillment | Medical Diagnostic Ordering |
| `lab_tech` | **Laboratory Technologist** | Diagnostic Specimen Processing, Lab Findings Entry, Review Loop Return | Clinical Discharges |
| `staff` | **Front Desk / Receptionist** | Patient Arrival Check-In, Walk-In Token Dispensing, Appointment Reception | Clinical Diagnostics & Discharges |
| `admin` | **Hospital Administrator** | Staff Credentialing & Licensing Approval, Dynamic Privilege Extension, Security Center, Audit Trail | Clinical Medical Decisions |
| `patient` | **Outpatient** | Virtual Queue Monitoring, Specialist Appointments, Discharge Summary Access | Internal Clinical Notes |

---

## 3. Functional Requirements

### 3.1 Medical Staff Self-Onboarding & Credentialing (FR-ONB)
- **FR-ONB-01**: Medical staff applicants must submit their full legal name, practicing medical license number (e.g. `MDC-GH-78492`), clinical specialty, and standby contact numbers during self-service onboarding (`/staff/onboarding`).
- **FR-ONB-02**: All medical staff accounts created by public self-registration or admin batch provisioning must remain in an inactive/pending review state (`is_approved = false`) until verified by a Hospital Administrator.
- **FR-ONB-03**: Administrators shall have a dedicated **"Pending Staff Approvals"** portal (`/admin/users?status=pending`) with one-click **"Verify & Approve"** and **"Revoke Access"** capabilities.

### 3.2 Dynamic Administrator Privilege Extension (FR-EXT)
- **FR-EXT-01**: Administrators shall have the authority to grant granular capability overrides to any individual user account via the profile edit panel (`/admin/users/{user}/edit`), including:
  - `can_consult`: Authorize Medical Consultation & Discharges
  - `can_triage`: Authorize 5-Tier Emergency Triage
  - `can_execute_lab`: Authorize Diagnostic Laboratory Findings Entry
  - `can_assign_beds`: Authorize Ward Bed & Bay Allocations

### 3.3 Security Telemetry & Mandatory Password Protection (FR-SEC)
- **FR-SEC-01**: Any user account provisioned by an administrator or receiving an administrative password reset must be flagged with `must_change_password = true`.
- **FR-SEC-02**: The `EnsurePasswordIsChanged` middleware shall intercept all authenticated HTTP requests and mandate personal password establishment at `/force-password-change` before granting portal access.
- **FR-SEC-03**: The system shall track client IP addresses on login. When a new or unrecognized IP address is detected, the engine shall dispatch a real-time **Security Notice Email** to the account holder and log an event in `security_alerts`.

### 3.4 Live Multi-Channel Notification Engine (FR-NOT)
- **FR-NOT-01**: The system shall interface with **Zoho Mail SMTP** (`smtp.zoho.com:465` SSL) to dispatch structured, responsive HTML notifications with **UGMC Hospital Branding** for:
  - Queue Token Issuance & Position Updates
  - Doctor Consultation Room Callouts
  - Diagnostic Lab Orders & Specimen Collection Notices
  - Diagnostic Findings Uploaded & Review Paging
  - Consultation Concluded & Final Care Summaries
  - Specialist Clinic Appointment Bookings & Reminders
  - 🚨 Emergency Trauma Code Red Broadcasts
  - Medical Staff Credentialing Approvals
  - HIPAA & ISO 27001 Security Telemetry Alerts
  - Inter-Staff STAT Consult Requests

---

## 4. Non-Functional Security & Compliance Requirements

- **NFR-SEC-01 (HIPAA PHI Confidentiality)**: All medical consultation notes, diagnostic findings, and patient identifiers must be shielded behind role-based and permission gates.
- **NFR-SEC-02 (ISO 27001 Immutability)**: All clinical actions, queue status mutations, bed allocations, user updates, and failed authentication attempts must be recorded in an append-only `audit_logs` table with actor ID, IP address, and metadata payload.
- **NFR-SEC-03 (Fault-Tolerant Session Management)**: Authentication routes shall handle session expirations gracefully via exception intercepts on `TokenMismatchException` and accept dual-method `GET`/`POST` requests on `/logout` to prevent HTTP 419 errors.

---

## 5. Verification & Test Coverage Matrix

- **Total Automated Test Suites**: 8 Test Files (`tests/Feature/`)
- **Total Automated Tests**: 68 Tests
- **Total Assertions**: 276 Assertions
- **Automated Regression Status**: 🟢 **100% Passing (68/68 Tests)**

---

*MediQueue Software Engineering Capstone &copy; 2026 — University of Ghana Medical Centre (UGMC).*
