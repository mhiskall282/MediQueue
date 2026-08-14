# MediQueue — Software Requirements Specification (SRS)

**Document ID**: SRS-001  
**Version**: 1.0  
**Date**: 2026-08-14  
**Project**: MediQueue — Smart Clinic Queue Management System  
**Author**: Software Engineering Examination Candidate  

---

## 1. Introduction

### 1.1 Purpose
This Software Requirements Specification (SRS) defines the functional and non-functional requirements for **MediQueue**, a web-based clinic queue management system. It serves as the primary contractual document between stakeholders and the development team, and constitutes the requirements baseline for the 48-hour individual software engineering capstone examination.

### 1.2 Scope
MediQueue is a web application that digitalises the patient queue workflow in small-to-medium outpatient clinics. The system enables patients to join clinic service queues digitally, receive unique queue numbers, monitor queue status in real time, and be called for service by clinic staff.

**In-Scope**:
- Patient self-service registration and queue joining
- Staff queue operations (call, serve, complete, skip)
- Admin service and user management
- Real-time queue position monitoring
- In-app notifications
- Audit logging

**Explicitly Out-of-Scope**:
- Electronic Medical Records (EMR)
- Clinical diagnosis or decision support
- Prescription management
- Payment processing
- Insurance / billing
- Laboratory management
- Telemedicine / video consultation
- Appointment scheduling beyond queue management

### 1.3 Definitions

| Term | Definition |
|---|---|
| Queue Entry | A single patient's place in a service queue, with full lifecycle state |
| Queue Number | Human-readable alphanumeric identifier assigned at queue join (e.g. `GC-001`) |
| Queue Prefix | Service-level alphabetic prefix for queue numbers (e.g. `GC` for General Consultation) |
| Service | A clinic-offered healthcare service type with configurable parameters |
| State Transition | A legal change from one queue entry status to another |
| Staff | Clinic employees operating queue consoles (doctors, nurses, assistants) |
| Admin | System administrators with full platform access |

---

## 2. Product Overview

MediQueue provides a structured digital queue workflow to replace inefficient verbal announcements and physical queuing processes in outpatient clinics.

### 2.1 Problem Statement

Small clinics typically manage patient flow via physical queues, verbal announcements, or paper-based numbering systems. This creates:
- Long, uncertain waiting periods with no position visibility
- Overcrowding at reception desks
- Inefficient staff-to-patient coordination
- No historical queue performance data
- Poor visibility into service demand peaks

MediQueue solves these problems through digital queue management with real-time status visibility.

### 2.2 Product Goals

1. Allow patients to join service queues digitally and monitor their position
2. Enable clinical staff to manage queue flow efficiently from a dashboard
3. Give administrators full operational visibility and configuration control
4. Maintain a complete, auditable record of all queue activity

---

## 3. Stakeholders

| Stakeholder | Role | Primary Interest |
|---|---|---|
| Clinic Owner | Business sponsor | ROI, operational efficiency |
| System Administrator | Platform operator | Configuration, user management |
| Healthcare Staff | System operator | Efficient queue management |
| Patients | End users | Reduced wait, queue visibility |
| IT Department | Infrastructure | Security, maintainability |
| Examination Evaluator | Academic assessor | SE rigor, documentation quality |

---

## 4. User Classes

### 4.1 Patient
Registered end-users of the clinic. Interact via mobile/web browser.
- Can register, login, join queues, monitor position, cancel queue
- Low technical sophistication assumed — UI must be simple and clear
- Primarily mobile device users

### 4.2 Staff (Doctor / Nurse / Assistant)
Clinic employees who operate consultation counters.
- Require high-density operational dashboards
- Must be able to quickly call patients, start service, complete, skip
- Primarily desktop/tablet users

### 4.3 Administrator
Clinic management or IT administrators.
- Full system access including service configuration, user management
- Monitor analytics, audit logs, system health
- Primarily desktop users

---

## 5. Functional Requirements

Requirements are labelled with priority:  
**[M]** = Must Have | **[S]** = Should Have | **[C]** = Could Have | **[W]** = Won't Have (this version)

### 5.1 Authentication & Account Management

| ID | Requirement | Priority |
|---|---|:---:|
| REQ-AUTH-001 | The system shall allow new patients to register with name, email, password | M |
| REQ-AUTH-002 | The system shall authenticate users via email/password with secure session | M |
| REQ-AUTH-003 | The system shall hash all passwords using bcrypt | M |
| REQ-AUTH-004 | The system shall support logout, invalidating session | M |
| REQ-AUTH-005 | The system shall restrict access to authenticated users for all patient/staff/admin routes | M |
| REQ-AUTH-006 | The system shall enforce role-based access control (patient / staff / admin) | M |
| REQ-AUTH-007 | The system shall provide password reset via email (email driver: log in exam) | S |
| REQ-AUTH-008 | The system shall allow admin to deactivate user accounts | S |

### 5.2 Service Management (Admin)

| ID | Requirement | Priority |
|---|---|:---:|
| REQ-SVC-001 | The system shall allow admin to create clinic services with: name, description, prefix, avg_duration_minutes | M |
| REQ-SVC-002 | The system shall allow admin to edit existing service details | M |
| REQ-SVC-003 | The system shall allow admin to activate or deactivate a service | M |
| REQ-SVC-004 | The system shall prevent patients from joining inactive services | M |
| REQ-SVC-005 | The system shall prevent deletion of services with historical queue entries | M |

### 5.3 Queue Management — Patient

| ID | Requirement | Priority |
|---|---|:---:|
| REQ-QUEUE-P-001 | The system shall display all active clinic services to authenticated patients | M |
| REQ-QUEUE-P-002 | The system shall allow a patient to join a queue for an active service | M |
| REQ-QUEUE-P-003 | The system shall generate a unique queue number on join (format: PREFIX-SEQ, e.g. GC-001) | M |
| REQ-QUEUE-P-004 | The system shall prevent a patient from having two active queue entries for the same service | M |
| REQ-QUEUE-P-005 | The system shall display the patient's queue position (number ahead, current serving) | M |
| REQ-QUEUE-P-006 | The system shall display an estimated wait time (people_ahead × avg_duration_minutes) | M |
| REQ-QUEUE-P-007 | The system shall allow a patient to cancel a WAITING queue entry | M |
| REQ-QUEUE-P-008 | The system shall display queue history to the patient | S |
| REQ-QUEUE-P-009 | The system shall notify the patient when their queue is called | S |

### 5.4 Queue Management — Staff

| ID | Requirement | Priority |
|---|---|:---:|
| REQ-QUEUE-S-001 | The system shall allow staff to view waiting queues for their service | M |
| REQ-QUEUE-S-002 | The system shall allow staff to call the next WAITING patient | M |
| REQ-QUEUE-S-003 | The system shall set the queue entry status to CALLED when called | M |
| REQ-QUEUE-S-004 | The system shall allow staff to start service (CALLED → IN_SERVICE) | M |
| REQ-QUEUE-S-005 | The system shall allow staff to complete service (IN_SERVICE → COMPLETED) | M |
| REQ-QUEUE-S-006 | The system shall allow staff to skip a CALLED patient (CALLED → SKIPPED) | M |
| REQ-QUEUE-S-007 | The system shall allow staff to recall a SKIPPED patient (SKIPPED → CALLED) | S |
| REQ-QUEUE-S-008 | The system shall display queue statistics to staff (waiting count, completed today, avg wait) | M |
| REQ-QUEUE-S-009 | The system shall enforce valid state transitions and reject invalid ones | M |

### 5.5 Admin Dashboard

| ID | Requirement | Priority |
|---|---|:---:|
| REQ-ADMIN-001 | The system shall display a real-time admin dashboard with key metrics | M |
| REQ-ADMIN-002 | The system shall display all queue activity across all services | M |
| REQ-ADMIN-003 | The system shall allow admin to view, filter, and manage user accounts | M |
| REQ-ADMIN-004 | The system shall allow admin to assign staff role to users | M |
| REQ-ADMIN-005 | The system shall display a searchable/filterable audit log | M |

### 5.6 Notifications

| ID | Requirement | Priority |
|---|---|:---:|
| REQ-NOTIF-001 | The system shall create in-app notifications for: queue joined, queue called, service started, completed | M |
| REQ-NOTIF-002 | The system shall display unread notification count in the navigation bar | S |
| REQ-NOTIF-003 | The system shall allow users to mark notifications as read | S |
| REQ-NOTIF-004 | The system shall architect notifications to support email/SMS delivery in future | S |

### 5.7 Audit Logging

| ID | Requirement | Priority |
|---|---|:---:|
| REQ-AUDIT-001 | The system shall create audit log entries for: service create/edit/toggle, queue call/serve/complete/skip | M |
| REQ-AUDIT-002 | Each audit record shall contain: actor, action, entity type, entity ID, metadata, IP, timestamp | M |
| REQ-AUDIT-003 | Audit logs shall be immutable (no edit/delete) | M |

---

## 6. Non-Functional Requirements

| ID | Requirement | Category | Priority |
|---|---|---|:---:|
| REQ-NFR-001 | System shall respond to user actions within 500ms under normal clinic load | Performance | M |
| REQ-NFR-002 | System shall support concurrent access by up to 50 simultaneous users | Scalability | M |
| REQ-NFR-003 | System shall be accessible at WCAG 2.1 AA standard | Accessibility | M |
| REQ-NFR-004 | System shall function on Chrome, Firefox, Safari, Edge (latest) | Compatibility | M |
| REQ-NFR-005 | System shall be responsive: mobile (375px+), tablet (768px+), desktop (1280px+) | Responsive | M |
| REQ-NFR-006 | Passwords shall be hashed using bcrypt | Security | M |
| REQ-NFR-007 | All forms shall include CSRF protection | Security | M |
| REQ-NFR-008 | System shall use HTTPS in production | Security | M |
| REQ-NFR-009 | System shall not expose stack traces in production (APP_DEBUG=false) | Security | M |
| REQ-NFR-010 | System shall use environment variables for all secrets and config | Security | M |
| REQ-NFR-011 | System shall paginate long lists (queue history, audit logs, user lists) | Usability | S |

---

## 7. System Constraints

1. **Technology Stack**: Laravel + Blade + Tailwind CSS + Vite (no SPA framework)
2. **Time Constraint**: 48-hour individual examination (single developer)
3. **No Medical Data**: System must not store clinical diagnosis, prescription, or health record data
4. **Database**: Relational database (SQLite for development, MySQL/PostgreSQL for production)

---

## 8. Assumptions

1. Patients have access to a smartphone or computer with a modern web browser
2. The clinic has a stable internet connection at reception and consultation counters
3. Staff are familiar with basic web navigation
4. Queue sequence numbers reset daily per service
5. Email delivery is not functional during examination — in-app notifications only
6. Multiple staff members may serve the same service concurrently

---

## 9. Requirements Traceability Matrix

| Req ID | Description | Component | Test Case | Priority |
|---|---|---|---|:---:|
| REQ-AUTH-001 | Patient registration | `Auth/RegisterController` | `AuthTest::test_patient_can_register` | M |
| REQ-AUTH-002 | User login | `Auth/LoginController` | `AuthTest::test_user_can_login` | M |
| REQ-AUTH-006 | RBAC enforcement | `RoleMiddleware` | `AuthorizationTest::*` | M |
| REQ-SVC-001 | Create service | `Admin/ServiceController` | `ServiceTest::test_admin_can_create_service` | M |
| REQ-QUEUE-P-002 | Join queue | `Patient/QueueController` | `QueueTest::test_patient_can_join_queue` | M |
| REQ-QUEUE-P-003 | Queue number generation | `QueueService::generateNumber` | `QueueTest::test_queue_number_generated` | M |
| REQ-QUEUE-P-004 | Duplicate prevention | `QueueService::join` | `QueueTest::test_duplicate_queue_prevented` | M |
| REQ-QUEUE-P-005 | Queue position | `QueueService::getPosition` | `QueueTest::test_queue_position_accurate` | M |
| REQ-QUEUE-P-006 | Estimated wait | `QueueService::getEstimatedWait` | `QueueTest::test_estimated_wait_calculated` | M |
| REQ-QUEUE-S-002 | Call next | `Staff/QueueController` | `QueueTest::test_staff_can_call_next` | M |
| REQ-QUEUE-S-009 | State transition enforcement | `QueueService::validateTransition` | `QueueTest::test_invalid_transition_rejected` | M |
| REQ-AUDIT-001 | Audit logging | `AuditLog::record` | `AuditTest::test_audit_log_created` | M |
