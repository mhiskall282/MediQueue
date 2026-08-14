# ADR-001: MediQueue Project Scope Definition

* **Status**: Accepted
* **Date**: 2026-08-14
* **Deciders**: Lead Software Engineering Agent & Examination Candidate

---

## Context & Problem Statement

MediQueue is being developed as an individual software engineering university examination project under a strict 48-hour time constraint. The application must demonstrate end-to-end software engineering discipline (Requirements, Estimation, Design, Code, Tests, Debt, Deployment, Manual) while delivering a fully functional clinic queue management system.

We must decide the explicit boundary of features to include and features to exclude to ensure project completion with high quality.

---

## Considered Options

1. **Option A: Full Healthcare EMR Suite**: Build Queue Management + Electronic Medical Records (EMR) + Prescription System + Billing/Payments.
2. **Option B: Dedicated Queue Management System**: Focus strictly on Patient Kiosk Check-In, Atomic Ticket Generation, Live TV Waiting Display, Staff Ticket Calling Dashboard, and Admin Service Management.
3. **Option C: Minimal MVP Kiosk**: Single-screen patient check-in only without staff role controls or administrative panels.

---

## Decision Outcome

**Chosen Option**: **Option B (Dedicated Queue Management System)**.

### Rationale:
- Option B directly fulfills all core requirements of clinic queue management while fitting comfortably within the 48-hour individual development constraint.
- Option A introduces excessive complexity (HIPAA/GDPR medical record compliance, payment gateway dependencies) that risks project failure within 48 hours.
- Option C is under-scoped and fails to demonstrate comprehensive software engineering analysis, role authorization, or complex state transitions required for top examination marks.

---

## Consequences

* **Positive**:
  - High focus on queue concurrency, atomic ticket sequence generation, and UI polish.
  - Complete coverage of all 13 software engineering evaluation phases (50 marks).
* **Negative / Technical Debt**:
  - Medical diagnosis, doctor clinical notes, and billing integrations are excluded and recorded as post-exam future evolution items.
