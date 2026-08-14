# MediQueue — System Analysis & Design

**Document ID**: SAD-001  
**Version**: 1.2  
**Date**: 2026-08-14  
**Live Production URL**: [https://mediqueue-25vl.onrender.com](https://mediqueue-25vl.onrender.com)  

---

## 1. System Context

MediQueue operates as a standalone web application within a clinic's local/cloud network. It interfaces with:

- **Web Browser (Patient)**: Mobile and desktop HTTP clients
- **Web Browser (Staff)**: Desktop and tablet HTTP clients
- **Web Browser (Admin)**: Desktop HTTP clients
- **Database**: Relational store (PostgreSQL / SQLite / MySQL)

```mermaid
graph TD
    subgraph External["External Actors"]
        P["Patient (Mobile or Web)"]
        S["Staff (Desktop or Tablet)"]
        A["Admin (Desktop)"]
    end

    subgraph CoreApp["MediQueue Web Application"]
        WEB["Web Layer (Controllers & Routes)"]
        BIZ["Domain Service (QueueService)"]
        DATA["Data Layer (Eloquent Models)"]
        DB[("Database (PostgreSQL / SQLite)")]
    end

    subgraph Notifications["Notification & Audit Subsystems"]
        MAIL["Transactional Email Service"]
        AUDIT["Immutable Audit Trail"]
    end

    P -->|HTTPS| WEB
    S -->|HTTPS| WEB
    A -->|HTTPS| WEB
    WEB --> BIZ
    BIZ --> DATA
    DATA --> DB
    BIZ --> MAIL
    BIZ --> AUDIT
```

---

## 2. Use Case Architecture

```mermaid
graph LR
    subgraph Actors["System Actors"]
        PAT["Patient"]
        STF["Clinical Staff"]
        ADM["Administrator"]
    end

    subgraph PatientScope["Patient Functions"]
        UC1["UC-01: Register & Login"]
        UC2["UC-02: View Services"]
        UC3["UC-03: Join Queue & Get Ticket"]
        UC4["UC-04: Monitor Live Position"]
        UC5["UC-05: Cancel Ticket"]
        UC6["UC-06: View Visit History"]
    end

    subgraph StaffScope["Staff Operations"]
        UC7["UC-07: Queue Operations Console"]
        UC8["UC-08: Call Next Patient"]
        UC9["UC-09: Start & Complete Consultation"]
        UC10["UC-10: Skip & Recall Patient"]
    end

    subgraph AdminScope["Administrative Governance"]
        UC11["UC-11: Manage Service Catalogue"]
        UC12["UC-12: Manage Users & Passwords"]
        UC13["UC-13: System Settings & Hours"]
        UC14["UC-14: Inspect Audit Trail"]
    end

    PAT --> UC1
    PAT --> UC2
    PAT --> UC3
    PAT --> UC4
    PAT --> UC5
    PAT --> UC6

    STF --> UC1
    STF --> UC7
    STF --> UC8
    STF --> UC9
    STF --> UC10

    ADM --> UC1
    ADM --> UC11
    ADM --> UC12
    ADM --> UC13
    ADM --> UC14
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
    User ||--o{ QueueEntry : "places or serves"
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
    participant DB as Relational Database

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
    participant DB as Relational Database

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
graph TD
    subgraph Presentation["Presentation Layer (Blade & Tailwind CSS)"]
        UI_PUBLIC["Public Landing & /docs"]
        UI_PATIENT["Patient Portal & Ticket Live Polling"]
        UI_STAFF["Staff Console & Actions"]
        UI_ADMIN["Admin Control & Settings"]
        UI_DISP["Hospital TV Screen (/display)"]
    end

    subgraph Routing["Routing & Middleware Layer"]
        ROUTES["routes/web.php"]
        AUTH_MID["auth Middleware"]
        ROLE_MID["RoleMiddleware (patient, staff, admin)"]
        THROTTLE["RateLimiter Throttling"]
    end

    subgraph Controllers["Controller Layer"]
        C_AUTH["Auth Controllers"]
        C_PAT["Patient Controllers"]
        C_STF["Staff Controllers"]
        C_ADM["Admin Controllers"]
        C_DISP["Display Controller"]
    end

    subgraph ServiceLayer["Business Domain Layer"]
        QS["QueueService (Pessimistic Locking & Transactions)"]
    end

    subgraph DataLayer["Persistence Layer"]
        MODELS["Eloquent Models (User, Service, QueueEntry, Setting, AuditLog)"]
        DB_STORE[("PostgreSQL / SQLite Database")]
    end

    Presentation --> Routing
    Routing --> Controllers
    Controllers --> ServiceLayer
    ServiceLayer --> DataLayer
    DataLayer --> DB_STORE
```
