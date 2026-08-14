# Domain Context: MediQueue

## 1. Core Domain Entities

1. **Service (Medical Service Category)**:
   - Represents a clinical service offered by the facility (e.g., General Consultation, Triage, Vaccination, Laboratory, Pharmacy).
   - Attributes: `name`, `code` (prefix e.g. "GC", "VAC", "TRI"), `description`, `avg_duration_minutes`, `is_active`.

2. **Patient**:
   - Represents a clinic attendee.
   - Attributes: `name`, `phone`, `national_id_or_passport`, `email` (optional), `patient_code`.

3. **Queue Ticket**:
   - The central transactional entity representing a patient's position in line for a specific service.
   - Attributes: `ticket_number` (e.g. `GC-014`), `patient_id`, `service_id`, `room_id` (nullable), `staff_id` (nullable), `status`, `position_number`, `estimated_call_time`, `called_at`, `served_at`, `completed_at`.

4. **Consultation Room / Counter**:
   - Physical location where service is rendered.
   - Attributes: `room_number`, `name` (e.g. "Room 102 - Triage"), `service_id`, `is_active`.

5. **User (Staff / Admin)**:
   - Authorized system users with roles (`admin`, `receptionist`, `doctor`, `nurse`).

---

## 2. Queue Ticket State Transitions

```
[ Patient Registers ]
         |
         v
     +--------+
     | WAITING|  <-- Assigned ticket_number, position_number
     +--------+
         |
         +-----------------------+
         | (Staff calls ticket)  | (Patient leaves / cancels)
         v                       v
     +--------+             +-----------+
     | CALLED |             | CANCELLED |
     +--------+             +-----------+
         |
         +-----------------------+
         | (Patient enters room) | (No response after 3 calls)
         v                       v
     +---------+            +-----------+
     | SERVING |            |  NO-SHOW  |
     +---------+            +-----------+
         |                       |
         | (Consultation done)   | (Re-queued by Reception)
         v                       v
    +-----------+           +-----------+
    | COMPLETED |           |  WAITING  |
    +-----------+           +-----------+
```

### Valid State Transitions:
- `WAITING` -> `CALLED`
- `WAITING` -> `CANCELLED`
- `CALLED` -> `SERVING`
- `CALLED` -> `NO-SHOW`
- `CALLED` -> `WAITING` (Re-queue)
- `SERVING` -> `COMPLETED`
- `SERVING` -> `TRANSFERRED` (Transferred to another service)

---

## 3. Business Rules

1. **Daily Ticket Reset**: Ticket numbers start at `001` each day for each service.
2. **One Active Ticket Per Service**: A patient cannot hold multiple `WAITING` or `CALLED` tickets for the *same* service concurrently.
3. **Priority Overrides**: Reception desk can mark a ticket as `EMERGENCY` or `HIGH_PRIORITY`, moving it to the top of the `WAITING` queue.
4. **Estimated Wait Calculation**: `Estimated Wait Time = (Position in Queue ahead of ticket) * (Service Average Duration Minutes) / (Number of active counters serving that service)`.
