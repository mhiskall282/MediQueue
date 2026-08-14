---
name: documentation
description: Technical documentation creation, SRS maintenance, API contract design, and User Manual writing for MediQueue.
---

# Documentation Skill Guide

Use this skill when drafting or updating project documentation, SRS specs, API documentation, or user manuals for **MediQueue**.

---

## 1. Documentation Structure & Standards

MediQueue examination documentation must be structured logically in `docs/`:

```
docs/
├── 01-REQUIREMENTS-SPECIFICATION-SRS.md
├── 02-SOFTWARE-ESTIMATION.md
├── 03-SYSTEM-ANALYSIS-AND-DESIGN.md
├── 04-IMPLEMENTATION-GUIDE.md
├── 05-TESTING-AND-QA-REPORT.md
├── 06-TECHNICAL-DEBT-LOG.md
├── 07-DEPLOYMENT-GUIDE.md
├── 08-USER-MANUAL.md
└── 09-MAINTENANCE-AND-EVOLUTION.md
```

---

## 2. User Manual Layout Guide

The **User Manual (`docs/08-USER-MANUAL.md`)** MUST include step-by-step instructions for three user personas:

1. **Patients**:
   - How to register at the clinic kiosk screen.
   - How to interpret the digital queue ticket number.
   - How to track real-time queue position on a mobile device or waiting room TV.

2. **Healthcare Staff (Doctors / Nurses)**:
   - How to log into the staff queue dashboard.
   - How to select a consultation room and service assignment.
   - How to call the next patient, mark consultation complete, or flag a no-show.

3. **Clinic Administrators**:
   - How to manage medical services (name, code, average duration, active status).
   - How to create staff accounts and assign room counters.
   - How to view daily queue volume analytics.
