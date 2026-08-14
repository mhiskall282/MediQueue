# Next Tasks Backlog

This backlog lists the prioritized next actions for upcoming development sprints.

---

## High Priority Next Tasks

1. **[TASK-001] Initialize Laravel Application Scaffolding**:
   - Initialize fresh Laravel application in current repository root using PHP 8.2 and Composer.
   - Configure SQLite database in `.env` and `config/database.php`.

2. **[TASK-002] Create Core Database Migrations & Models**:
   - Create migrations and Eloquent models for `User`, `Service`, `Room`, `Patient`, `QueueTicket`, and `AuditLog`.
   - Implement `DatabaseSeeder` with default admin/staff accounts and demo services.

3. **[TASK-003] Implement Atomic Queue Ticket Generation Service**:
   - Create `app/Services/QueueTicketService.php` with `DB::transaction()` and pessimistic locking (`lockForUpdate()`).
   - Write unit tests for ticket sequence generation (`GC-001`, `GC-002`).

4. **[TASK-004] Build Patient Kiosk & Digital Ticket View**:
   - Create `KioskController`, Form Request validation, and Blade kiosk templates (`resources/views/kiosk/`).
   - Implement patient queue status monitor page.

5. **[TASK-005] Build Staff Queue Dashboard & TV Display**:
   - Implement `StaffQueueController` for calling next patient, marking completed, or recording no-show.
   - Build live TV waiting room display page (`/display`).
