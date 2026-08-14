---
name: software-estimation
description: Use Case Points (UCP) and Function Point Analysis (FPA) software estimation methodology for MediQueue.
---

# Software Effort Estimation Skill Guide

Use this skill to perform, document, or verify software effort estimation for **MediQueue**.

---

## 1. Preferred Method: Use Case Points (UCP)

MediQueue utilizes the standardized **Use Case Points (UCP)** model created by Gustav Karner to calculate total software development effort.

### Formula:
$$\text{UCP} = (\text{UUCP}) \times \text{TCF} \times \text{ECF}$$
$$\text{Total Effort (Hours)} = \text{UCP} \times \text{PF}$$

Where:
- $\text{UUCP}$ = Unadjusted Use Case Points ($\text{UAW} + \text{UUCW}$)
- $\text{UAW}$ = Unadjusted Actor Weight
- $\text{UUCW}$ = Unadjusted Use Case Weight
- $\text{TCF}$ = Technical Complexity Factor ($0.6 + 0.01 \times \sum (T_i \times W_i)$)
- $\text{ECF}$ = Environmental Complexity Factor ($1.4 + (-0.03) \times \sum (E_i \times W_i)$)
- $\text{PF}$ = Productivity Factor (20 Person-Hours per UCP)

---

## 2. Weight Classification Rules

### Unadjusted Actor Weight (UAW):
* **Simple (Weight = 1)**: Automated API or system interface (e.g. System Cron Timer).
* **Average (Weight = 2)**: Interactive command-line interface or structured API.
* **Complex (Weight = 3)**: Human user via Web Graphical Interface / Kiosk (e.g. Patient, Staff, Admin).

### Unadjusted Use Case Weight (UUCW):
* **Simple (Weight = 5)**: $\le 3$ transactions / steps (e.g. View Service List).
* **Average (Weight = 10)**: 4 to 7 transactions / steps (e.g. Patient Kiosk Check-in & Ticket Issue).
* **Complex (Weight = 15)**: $> 7$ transactions / steps with concurrency control & status shifts (e.g. Staff Ticket Calling & Real-Time Queue State Engine).

---

## 3. MediQueue Standard UCP Calculation Baseline

```markdown
### 1. Actor Weights (UAW)
- Patient (Complex) = 3
- Receptionist (Complex) = 3
- Healthcare Staff (Complex) = 3
- Administrator (Complex) = 3
Total UAW = 12

### 2. Use Case Weights (UUCW)
- UC-1: Kiosk Patient Registration & Ticket Generation (Average) = 10
- UC-2: Live Queue Waiting Room TV Display (Simple) = 5
- UC-3: Staff Queue Management & Ticket Calling (Complex) = 15
- UC-4: Admin Service & Room Configuration (Average) = 10
- UC-5: User Authentication & Role Authorization (Simple) = 5
- UC-6: Analytics & Audit Logging (Simple) = 5
Total UUCW = 50

### 3. Total Unadjusted UCP (UUCP)
UUCP = UAW (12) + UUCW (50) = 62

### 4. Technical & Environmental Adjustments
- TCF (Technical Complexity Factor) = 0.85 (High reliability, concurrency, performance needs)
- ECF (Environmental Complexity Factor) = 0.80 (High developer expertise, familiar Laravel stack)

### 5. Final Adjusted UCP & Effort
- Adjusted UCP = 62 * 0.85 * 0.80 = 42.16 UCP
- Nominal Effort = 42.16 * 20 Hours = 843.2 Person-Hours (Standard Team Project)

### 6. Scope Reduction Mapping for 48-Hour Exam Scope
- Exam Mode: Individual Developer + AI Agent Pair Programming
- Effective Productivity Multiplier: 18x acceleration via automated scaffolding, pre-built Blade UI, Eloquent ORM.
- Calculated Single-Developer Workload: ~42 Hours active development + 6 Hours contingency buffer = 48 Hours Total.
```

---

## 4. Estimation Artifact Rules

* **Preserve Evidence**: Estimation documents must be created during the requirements analysis phase and preserved as evidence in `docs/02-SOFTWARE-ESTIMATION.md`.
* **Zero Post-hoc Tweaking**: Never retroactively alter raw baseline equations after project completion. Document any variance between estimated vs actual hours in the project retrospective.
