# MediQueue — System Analysis & Design

**Document ID**: SAD-001  
**Version**: 1.1  
**Date**: 2026-08-14  

---

## 1. System Context

MediQueue operates as a standalone web application within a clinic's local/cloud network. It interfaces with:

- **Web Browser (Patient)**: Mobile and desktop HTTP clients
- **Web Browser (Staff)**: Desktop and tablet HTTP clients
- **Web Browser (Admin)**: Desktop HTTP clients
- **Database**: Relational store (SQLite / MySQL)

```mermaid
flowchart TB
    subgraph External["External Actors"]
        P["Patient (Mobile or Web Browser)"]
        S["Staff (Desktop or Tablet Browser)"]
        A["Admin (Desktop Browser)"]
    end

    subgraph MediQueue["MediQueue Web Application"]
        WEB["Web Layer (Laravel Routes and Controllers)"]
        BIZ["Business Layer (QueueService and Actions)"]
        DATA["Data Layer (Eloquent Models)"]
        DB[("Database (SQLite or MySQL)")]
    end

    subgraph Future["Future Integrations"]
        SMTP["SMTP Email Gateway"]
        SMS["SMS Notification Gateway"]
    end

    P -->|HTTP/HTTPS| WEB
    S -->|HTTP/HTTPS| WEB
    A -->|HTTP/HTTPS| WEB
    WEB --> BIZ
    BIZ --> DATA
    DATA --> DB
    BIZ -.->|Future| SMTP
    BIZ -.->|Future| SMS
```

---

## 2. Use Case Diagram

```mermaid
flowchart LR
    subgraph Users["System Actors"]
        PAT["Patient"]
        STF["Staff"]
        ADM["Administrator"]
    end

    subgraph PatientCases["Patient Use Cases"]
        UC1["UC-01: Register Account"]
        UC2["UC-02: Login and Logout"]
        UC3["UC-03: View Services"]
        UC4["UC-04: Join Service Queue"]
        UC5["UC-05: View Live Queue Position"]
        UC6["UC-06: Cancel Queue Ticket"]
        UC7["UC-07: View History"]
    end

    subgraph StaffCases["Staff Operations"]
        UC8["UC-08: View Queue Dashboard"]
        UC9["UC-09: Call Next Patient"]
        UC10["UC-10: Start Service"]
        UC11["UC-11: Complete Service"]
        UC12["UC-12: Skip Patient"]
        UC13["UC-13: Recall Patient"]
    end

    subgraph AdminCases["Administrative Control"]
        UC14["UC-14: Manage Services"]
        UC15["UC-15: Manage Users and Roles"]
        UC16["UC-16: View System Dashboard"]
        UC17["UC-17: View Immutable Audit Log"]
    end

    PAT --> UC1
    PAT --> UC2
    PAT --> UC3
    PAT --> UC4
    PAT --> UC5
    PAT --> UC6
    PAT --> UC7

    STF --> UC2
    STF --> UC8
    STF --> UC9
    STF --> UC10
    STF --> UC11
    STF --> UC12
    STF --> UC13

    ADM --> UC2
    ADM --> UC14
    ADM --> UC15
    ADM --> UC16
    ADM --> UC17
    ADM --> UC8
```

---

## 3. Queue State Machine

```mermaid
stateDiagram-v2
    [*] --> WAITING: Patient joins queue
    WAITING --> CALLED: Staff calls next
    WAITING --> CANCELLED: Patient cancels
    CALLED --> IN_SERVICE: Staff starts service
    CALLED --> SKIPPED: Staff skips patient
    SKIPPED --> CALLED: Staff recalls patient
    IN_SERVICE --> COMPLETED: Staff completes service
    COMPLETED --> [*]
    CANCELLED --> [*]
```

### Valid Transitions Table

| From State | To State | Trigger Actor | Action Event |
|---|---|---|---|
| `WAITING` | `CALLED` | Staff | Call Next button |
| `WAITING` | `CANCELLED` | Patient / Admin | Cancel ticket |
| `CALLED` | `IN_SERVICE` | Staff | Start Service button |
| `CALLED` | `SKIPPED` | Staff | Skip button (patient not present) |
| `SKIPPED` | `CALLED` | Staff | Recall button |
| `IN_SERVICE` | `COMPLETED` | Staff | Complete Service button |

**Invalid Transitions (Enforced by `QueueService`):**
- `COMPLETED` cannot transition to any other status (terminal state).
- `CANCELLED` cannot transition to any other status (terminal state).
- `IN_SERVICE` cannot transition directly to `WAITING` or `CANCELLED`.
- `COMPLETED` cannot transition to `WAITING`.

---

## 4. Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    User ||--o{ QueueEntry : "places / serves"
    User ||--o{ Notification : "receives"
    User ||--o{ AuditLog : "triggers"
    Service ||--o{ QueueEntry : "categorizes"

    User {
        bigint id PK
        string name
        string email UK
        string phone
        string role
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    Service {
        bigint id PK
        string name
        string description
        string prefix UK
        int avg_duration_minutes
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    QueueEntry {
        bigint id PK
        bigint patient_id FK
        bigint service_id FK
        bigint served_by FK
        string queue_number
        int sequence_number
        string status
        string priority
        timestamp joined_at
        timestamp called_at
        timestamp service_started_at
        timestamp completed_at
        timestamp cancelled_at
        timestamp skipped_at
    }

    Notification {
        bigint id PK
        bigint user_id FK
        string type
        string title
        string body
        json data
        timestamp read_at
    }

    AuditLog {
        bigint id PK
        bigint user_id FK
        string action
        string entity_type
        bigint entity_id
        json metadata
        string ip_address
        timestamp created_at
    }
```

---

## 5. Sequence Diagram: Patient Joins Queue

```mermaid
sequenceDiagram
    autonumber
    actor Patient as Patient
    participant Browser as Web Browser
    participant Controller as PatientQueueController
    participant Service as QueueService
    participant DB as SQLite Database

    Patient->>Browser: Selects Service and clicks "Join Queue"
    Browser->>Controller: POST /patient/queue (service_id)
    Controller->>Service: join(patient, service)
    Service->>DB: BEGIN TRANSACTION
    Service->>DB: Check for duplicate active entry (lockForUpdate)
    DB-->>Service: No duplicate found
    Service->>DB: Query MAX sequence_number for today (lockForUpdate)
    DB-->>Service: Current MAX = 4
    Service->>DB: INSERT queue_entry (queue_number='GC-005', sequence=5, status='WAITING')
    Service->>DB: INSERT notification (patient_id, title, body)
    Service->>DB: INSERT audit_log (action='queue.joined')
    Service->>DB: COMMIT TRANSACTION
    DB-->>Service: Entry GC-005 created
    Service-->>Controller: QueueEntry Object
    Controller-->>Browser: Redirect to /patient/queue/{id}/status
    Browser-->>Patient: Display Live Ticket GC-005 with real-time countdown
```

---

## 6. Sequence Diagram: Staff Calls Next Patient

```mermaid
sequenceDiagram
    autonumber
    actor Staff as Clinical Staff
    participant Browser as Staff Dashboard
    participant Controller as StaffQueueController
    participant Service as QueueService
    participant DB as SQLite Database

    Staff->>Browser: Clicks "Call Next Patient"
    Browser->>Controller: POST /staff/queue/call-next (service_id)
    Controller->>Service: callNext(service, staff)
    Service->>DB: BEGIN TRANSACTION
    Service->>DB: SELECT next WAITING ticket by priority then sequence (lockForUpdate)
    DB-->>Service: Next ticket found (GC-005)
    Service->>DB: UPDATE queue_entry SET status='CALLED', called_at=NOW(), served_by=staff_id
    Service->>DB: INSERT notification for patient ('You have been called')
    Service->>DB: INSERT audit_log (action='queue.called')
    Service->>DB: COMMIT TRANSACTION
    DB-->>Service: Updated record
    Service-->>Controller: QueueEntry (GC-005)
    Controller-->>Browser: Redirect / Flash Success
    Browser-->>Staff: Console displays GC-005 as Active Consultation
```

---

## 7. Layered Architecture Overview

```mermaid
flowchart TD
    subgraph Client["Presentation Layer (Blade + Tailwind CSS)"]
        UI_PUBLIC["Public Landing Page"]
        UI_AUTH["Split-Screen Auth Forms"]
        UI_PATIENT["Patient Portal & Live Polling Ticket"]
        UI_STAFF["Staff Clinical Operations Console"]
        UI_ADMIN["Admin Control Center & Audit Trail"]
    end

    subgraph Routing["Routing & Middleware Layer"]
        ROUTES["routes/web.php"]
        AUTH_MID["auth Middleware"]
        ROLE_MID["RoleMiddleware (patient, staff, admin)"]
        CSRF_MID["VerifyCsrfToken Middleware"]
    end

    subgraph Controllers["Controller Layer"]
        C_AUTH["Auth Controllers"]
        C_PAT["Patient Controllers"]
        C_STF["Staff Controllers"]
        C_ADM["Admin Controllers"]
    end

    subgraph ServiceLayer["Business Logic Layer"]
        QS["QueueService (Transactions, Atomic Numbering, Validation)"]
    end

    subgraph DataLayer["Data & Persistence Layer"]
        MODELS["Eloquent Models (User, Service, QueueEntry, Notification, AuditLog)"]
        DB_STORE[("Relational Database (SQLite / MySQL)")]
    end

    Client --> Routing
    Routing --> Controllers
    Controllers --> ServiceLayer
    ServiceLayer --> DataLayer
    DataLayer --> DB_STORE
```
