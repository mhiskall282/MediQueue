# Database Strategy & Schema Context

## 1. Database Architecture

* **Database Engine**: Relational (SQLite for local testing/development; MySQL 8.0+ / PostgreSQL ready for production).
* **ORM**: Laravel Eloquent.
* **Migration Integrity**: All schema changes must be implemented via strict, reversible Laravel migrations (`up()` and `down()`).

---

## 2. Core Relational Schema Strategy

### Tables:

1. **`users`**:
   - `id` (PK, bigint)
   - `name` (string)
   - `email` (string, unique)
   - `password` (string)
   - `role` (enum: 'admin', 'doctor', 'nurse', 'receptionist')
   - `remember_token`
   - `timestamps`

2. **`services`**:
   - `id` (PK, bigint)
   - `name` (string)
   - `code` (string, unique, e.g., 'GC', 'TRI')
   - `description` (text, nullable)
   - `avg_duration_minutes` (integer, default: 15)
   - `is_active` (boolean, default: true)
   - `timestamps`

3. **`rooms`**:
   - `id` (PK, bigint)
   - `room_number` (string)
   - `name` (string)
   - `service_id` (FK -> services.id)
   - `is_active` (boolean, default: true)
   - `timestamps`

4. **`patients`**:
   - `id` (PK, bigint)
   - `patient_code` (string, unique)
   - `name` (string)
   - `phone` (string)
   - `national_id` (string, nullable)
   - `email` (string, nullable)
   - `timestamps`

5. **`queue_tickets`**:
   - `id` (PK, bigint)
   - `ticket_number` (string) — e.g. "GC-005"
   - `sequence_number` (integer) — e.g. 5
   - `patient_id` (FK -> patients.id)
   - `service_id` (FK -> services.id)
   - `room_id` (FK -> rooms.id, nullable)
   - `staff_id` (FK -> users.id, nullable)
   - `status` (enum: 'WAITING', 'CALLED', 'SERVING', 'COMPLETED', 'CANCELLED', 'NO-SHOW')
   - `priority` (enum: 'NORMAL', 'HIGH', 'EMERGENCY', default: 'NORMAL')
   - `called_at` (timestamp, nullable)
   - `served_at` (timestamp, nullable)
   - `completed_at` (timestamp, nullable)
   - `timestamps`

6. **`audit_logs`**:
   - `id` (PK, bigint)
   - `user_id` (FK -> users.id, nullable)
   - `action` (string)
   - `details` (json, nullable)
   - `ip_address` (string, nullable)
   - `timestamps`

---

## 3. Indexing & Optimization

To ensure sub-millisecond query performance for public queue displays and staff dashboards:
* **Composite Index**: `queue_tickets(service_id, status, created_at)`
* **Daily Ticket Lookup Index**: `queue_tickets(service_id, sequence_number, created_at)`
* **Patient Active Ticket Index**: `queue_tickets(patient_id, status)`

---

## 4. Seeders & Testing Data

* **`DatabaseSeeder`** must run deterministically and populate:
  - Default Admin, Receptionist, Doctor, and Nurse accounts.
  - 4 Standard Medical Services (Triage, General Consultation, Vaccination, Pharmacy).
  - 4 Consultation Rooms.
  - Mock queue dataset spanning `WAITING`, `CALLED`, and `COMPLETED` states for demonstration.
