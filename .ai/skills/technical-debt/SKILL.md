---
name: technical-debt
description: Technical debt discovery, categorization, logging, and tracking protocols for MediQueue.
---

# Technical Debt Management Skill Guide

Use this skill when identifying, recording, prioritizing, or resolving technical debt for **MediQueue**.

---

## 1. Technical Debt Item Schema

Every technical debt item MUST use the following standardized format:

```markdown
### [TD-001] Synchronous Polling for Live Queue Displays
* **Debt**: Live TV waiting display and patient status monitor use client-side JS interval polling (`setInterval` every 5 seconds) rather than WebSockets / Server-Sent Events (SSE).
* **Cause**: 48-hour exam time constraint; avoiding WebSocket server configuration complexity (Pusher / Reverb / Laravel Echo setup overhead).
* **Impact**: Higher HTTP request frequency on server during peak clinic usage (up to 12 requests/min per display client). Minimal impact for small/medium clinics (< 20 displays).
* **Priority**: Acceptable temporarily
* **Proposed Resolution**: Migrate to Laravel Reverb / SSE in post-exam Version 2.0 release.
* **Status**: Open (Logged on Day 1)
```

---

## 2. Priority Classification Categories

1. **Acceptable Temporarily**:
   - Trade-offs intentionally introduced due to the 48-hour exam scope that have no negative impact on functional correctness or security (e.g. SQLite database instead of MySQL cluster, synchronous queue polling).

2. **Scheduled for Future Resolution**:
   - Enhancements required for multi-clinic enterprise scaling (e.g. multi-tenancy, SMS API integration, localized language translation).

3. **Critical / Immediate**:
   - Defect or vulnerability that must be fixed before final examination submission (e.g. potential concurrency race condition in ticket sequence generation, missing CSRF tokens).
