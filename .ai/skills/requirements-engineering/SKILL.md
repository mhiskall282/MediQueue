---
name: requirements-engineering
description: Requirements analysis, SRS creation, MoSCoW prioritization, acceptance criteria, and traceability matrix maintenance for MediQueue.
---

# Requirements Engineering Skill Guide

Use this skill when eliciting, defining, prioritizing, or documenting functional and non-functional requirements for **MediQueue**.

---

## 1. MoSCoW Prioritization Standard

Group every system requirement into one of the four MoSCoW categories:

* **Must Have (M)**: Non-negotiable core features required to pass the 48-hour exam (e.g. Patient Check-in Kiosk, Atomic Ticket Generation, Staff Calling Dashboard, Service Management).
* **Should Have (S)**: High-value features that significantly enhance usability (e.g. Real-time TV Waiting Room Display, Estimated Wait Time Algorithm, Priority Queue Override).
* **Could Have (C)**: Desirable polish features if time permits (e.g. Audio chime sound for ticket calling, SMS notifications stub).
* **Won't Have (W)**: Explicitly out-of-scope features for the 48-hour exam (e.g. EMR/EHR integration, medical diagnosis tools, payment gateway processing).

---

## 2. Requirements Structure & Formatting

Every requirement must use a unique ID and structured schema:

```markdown
### [REQ-F-001] Patient Self-Registration & Ticket Issue
* **Category**: Functional
* **Priority**: Must Have
* **User Story**: As a clinic patient, I want to select a medical service and enter my phone number at the kiosk so that I receive a unique sequential queue ticket.
* **Acceptance Criteria**:
  1. Kiosk displays all active clinical services with names and descriptions.
  2. Patient inputs full name and valid phone number.
  3. System atomically assigns ticket number formatted as `{SERVICE_CODE}-{SEQUENCE}` (e.g. `GC-005`).
  4. System prevents ticket creation if service is marked inactive.
* **Traceability**: Tested by `tests/Feature/Patient/KioskRegistrationTest.php`.
```

---

## 3. Requirements Traceability Matrix (RTM)

Maintain an RTM table in SRS documentation mapping requirements to design components and test cases:

| Req ID | Description | Priority | Design Component | Test Case | Status |
|---|---|---|---|---|---|
| `REQ-F-001` | Patient Kiosk Registration | Must Have | `KioskController@store` | `KioskRegistrationTest` | Completed |
| `REQ-F-002` | Atomic Ticket Sequence | Must Have | `QueueTicketService` | `TicketGeneratorTest` | Completed |
| `REQ-F-003` | Staff Ticket Calling | Must Have | `StaffQueueController@callNext` | `TicketCallingTest` | Completed |
| `REQ-F-004` | Service Management | Must Have | `AdminServiceController` | `ServiceManagementTest` | Completed |
