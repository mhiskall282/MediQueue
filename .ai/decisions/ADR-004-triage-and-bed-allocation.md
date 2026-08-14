# ADR-004: Multi-Level Emergency Triage and Hospital Bed Allocation

## Status
Accepted

## Context
High-volume hospital outpatient departments and emergency rooms require structured clinical acuity categorization to prioritize critically ill patients over routine consultations. Furthermore, hospital capacity requires real-time bed and bay tracking.

## Decision
1. Implement the **Manchester Triage System (5-Tier Protocol)**:
   - `RED` (Priority 1 - Resuscitation / Immediate)
   - `ORANGE` (Priority 2 - Very Urgent, 10 min target)
   - `YELLOW` (Priority 3 - Urgent, 60 min target)
   - `GREEN` (Priority 4 - Standard, 120 min target)
   - `BLUE` (Priority 5 - Non-Urgent, 240 min target)
2. Embed triage severity weights directly in `QueueEntry::scopeByQueueOrder()`, ensuring higher acuity patients naturally advance to the front of clinical queues.
3. Introduce the `Bed` entity with lifecycle states (`AVAILABLE`, `OCCUPIED`, `MAINTENANCE`) and automatic release hooks upon patient discharge.

## Consequences
- Dramatically reduced clinical wait times for emergency trauma cases.
- Transparent, audit-tracked bed occupancy rates for hospital administrators.
