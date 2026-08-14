---
name: system-analysis
description: System analysis, domain model analysis, workflow mapping, and state machine analysis for MediQueue.
---

# System Analysis Skill Guide

Use this skill when analyzing workflows, domain boundaries, state transitions, and system inputs/outputs for **MediQueue**.

---

## 1. State Machine Analysis: Queue Ticket Life Cycle

An essential component of MediQueue system analysis is mapping state transitions for `QueueTicket` entities to ensure invalid operations are rejected at the domain boundary.

```
       +--------------+
       |   WAITING    |
       +--------------+
         |          |
 (Staff Call)    (Cancel)
         |          |
         v          v
   +----------+  +-----------+
   |  CALLED  |  | CANCELLED |
   +----------+  +-----------+
     |      |
  (Start) (No-Show)
     |      |
     v      v
+---------+ +-----------+
| SERVING | |  NO-SHOW  |
+---------+ +-----------+
     |
 (Complete)
     |
     v
+-----------+
| COMPLETED |
+-----------+
```

---

## 2. Boundary Analysis & Input Validation Rules

* **Patient Registration Phone Number**:
  - Valid: `0123456789`, `+60123456789`, `(03) 9876 5432`.
  - Invalid: `abc`, `<script>`, empty string.
  - Action: Reject with 422 Unprocessable Entity form error message.

* **Ticket Number Generation Boundary**:
  - Range: `001` to `999` per service per day.
  - Reset condition: At `00:00:00` server local time or when service date shifts.

* **Concurrency Limits**:
  - Maximum concurrent tickets called per room = 1 ticket (`CALLED` or `SERVING`).
  - Attempting to call a new ticket when a room currently has a ticket in `SERVING` status prompts staff to complete or hold the active ticket first.
