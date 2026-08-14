# Architecture Context: MediQueue

## 1. High-Level Architectural Pattern

MediQueue follows a **Layered Monolith Architecture** built on Laravel 10/11, utilizing the Model-View-Controller (MVC) pattern enhanced with a dedicated **Service & Action Layer** for core domain workflows.

```
+-----------------------------------------------------------------------+
|                              PRESENTATION LAYER                       |
|   Blade Views / Tailwind CSS / Vanilla JS & Alpine.js Kiosk & Dashboard |
+-----------------------------------------------------------------------+
                                   |
                                   v
+-----------------------------------------------------------------------+
|                                ROUTING & AUTH                         |
|   Laravel Web Routes / Middleware (Auth, RoleMiddleware, CSRF Guard)  |
+-----------------------------------------------------------------------+
                                   |
                                   v
+-----------------------------------------------------------------------+
|                            CONTROLLER LAYER                           |
|   Thin Controllers (PatientQueueController, StaffQueueController, etc)|
+-----------------------------------------------------------------------+
                                   |
                                   v
+-----------------------------------------------------------------------+
|                         APPLICATION & SERVICE LAYER                   |
|   QueueTicketService / QueueAllocationAction / AnalyticsService       |
+-----------------------------------------------------------------------+
                                   |
                                   v
+-----------------------------------------------------------------------+
|                           DOMAIN / ELOQUENT LAYER                     |
|   Models (Patient, Service, QueueTicket, Room, User, AuditLog)        |
+-----------------------------------------------------------------------+
                                   |
                                   v
+-----------------------------------------------------------------------+
|                             PERSISTENCE LAYER                         |
|   Database (SQLite / MySQL) with Foreign Keys & DB Transactions       |
+-----------------------------------------------------------------------+
```

---

## 2. Component Boundaries & Responsibilities

1. **Controllers (`app/Http/Controllers/`)**:
   - Thin wrappers responsible for HTTP request handling, form request validation triggering, invoking Service/Action classes, and returning Blade views or JSON responses.
   - Controllers must NEVER contain database queries (`DB::table()` or direct `Model::where()`) or complex business logic.

2. **Form Requests (`app/Http/Requests/`)**:
   - Encapsulate all incoming input validation rules and authorization checks (`authorize()`, `rules()`).

3. **Services / Actions (`app/Services/`, `app/Actions/`)**:
   - Encapsulate business logic such as atomic ticket number generation (`A-001`, `B-002`), wait time calculation, queue status state transitions, and audit logging.
   - Use Database Transactions (`DB::transaction()`) to guarantee atomic operations.

4. **Eloquent Models (`app/Models/`)**:
   - Define table structures, relationships (`hasMany`, `belongsTo`), attribute casting, and local query scopes (`waiting()`, `called()`, `today()`).

5. **Blade Views & Components (`resources/views/`)**:
   - Layouts: `app.blade.php`, `kiosk.blade.php`, `display.blade.php`.
   - Components: `x-badge`, `x-card`, `x-button`, `x-queue-table`, `x-modal`, `x-navbar`.

---

## 3. Queue Concurrency & Transaction Security

Generating queue ticket numbers (e.g. Service A -> `A-001`, `A-002`) requires strict concurrency control:
* Use `DB::transaction()` with pessimistic lock (`lockForUpdate()`) on the target Service counter row or `QueueTicket::where('service_id', ...)->whereDate('created_at', today())->lockForUpdate()`.
* This prevents duplicate ticket numbers when multiple patients register simultaneously at different kiosk stations.
