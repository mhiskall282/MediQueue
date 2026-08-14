# MediQueue — System Analysis & Design

**Document ID**: SAD-001  
**Version**: 1.0  
**Date**: 2026-08-14  

---

## 1. System Context

MediQueue operates as a standalone web application within a clinic's local/cloud network. It interfaces with:

- **Web Browser (Patient)**: Mobile/desktop HTTP clients
- **Web Browser (Staff)**: Desktop/tablet HTTP clients
- **Web Browser (Admin)**: Desktop HTTP clients
- **SMTP Server** (future): Email notification delivery
- **Database**: Relational store (SQLite/MySQL)

```mermaid
graph TB
    subgraph External["External Actors"]
        P["👤 Patient\n(Mobile/Web Browser)"]
        S["👨‍⚕️ Staff\n(Desktop/Tablet Browser)"]
        A["🔧 Admin\n(Desktop Browser)"]
    end

    subgraph MediQueue["MediQueue Web Application"]
        direction TB
        WEB["Web Layer\n(Laravel Routes + Controllers)"]
        BIZ["Business Layer\n(QueueService + Actions)"]
        DATA["Data Layer\n(Eloquent Models)"]
        DB[("SQLite / MySQL\nDatabase")]
    end

    subgraph Future["Future Integrations"]
        SMTP["📧 SMTP Email Server"]
        SMS["📱 SMS Gateway"]
    end

    P -- "HTTP/HTTPS" --> WEB
    S -- "HTTP/HTTPS" --> WEB
    A -- "HTTP/HTTPS" --> WEB
    WEB --> BIZ
    BIZ --> DATA
    DATA --> DB
    BIZ -.->|"Future"| SMTP
    BIZ -.->|"Future"| SMS
```

---

## 2. Use Case Diagram

```mermaid
graph LR
    subgraph System["MediQueue System Boundary"]
        UC1["UC-01: Register"]
        UC2["UC-02: Login/Logout"]
        UC3["UC-03: View Services"]
        UC4["UC-04: Join Queue"]
        UC5["UC-05: View Queue Position"]
        UC6["UC-06: Cancel Queue"]
        UC7["UC-07: View History"]
        UC8["UC-08: View Queue Dashboard"]
        UC9["UC-09: Call Next Patient"]
        UC10["UC-10: Start Service"]
        UC11["UC-11: Complete Service"]
        UC12["UC-12: Skip Patient"]
        UC13["UC-13: Recall Patient"]
        UC14["UC-14: Manage Services"]
        UC15["UC-15: Manage Users"]
        UC16["UC-16: System Dashboard"]
        UC17["UC-17: View Audit Log"]
    end

    PAT["👤 Patient"]
    STF["👨‍⚕️ Staff"]
    ADM["🔧 Admin"]

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
    [*] --> WAITING : Patient joins queue
    WAITING --> CALLED : Staff calls next
    WAITING --> CANCELLED : Patient cancels
    CALLED --> IN_SERVICE : Staff starts service
    CALLED --> SKIPPED : Staff skips patient
    SKIPPED --> CALLED : Staff recalls patient
    IN_SERVICE --> COMPLETED : Staff completes service
    COMPLETED --> [*]
    CANCELLED --> [*]
```

### Valid Transitions Table

| From | To | Actor | Trigger |
|---|---|---|---|
| WAITING | CALLED | Staff | "Call Next" action |
| WAITING | CANCELLED | Patient / Staff | Patient cancels or reception overrides |
| CALLED | IN_SERVICE | Staff | "Start Service" action |
| CALLED | SKIPPED | Staff | "Skip" action (no response) |
| SKIPPED | CALLED | Staff | "Recall" action |
| IN_SERVICE | COMPLETED | Staff | "Complete" action |

**Invalid Transitions** (backend enforced):
- COMPLETED → any (terminal state)
- CANCELLED → any (terminal state)  
- IN_SERVICE → WAITING/CANCELLED
- COMPLETED → WAITING

---

## 4. Entity-Relationship Diagram

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin|staff|patient"
        boolean is_active
        timestamps created_at
        timestamps updated_at
    }

    SERVICES {
        bigint id PK
        string name
        string description
        string prefix UK
        int avg_duration_minutes
        boolean is_active
        timestamps created_at
        timestamps updated_at
    }

    QUEUE_ENTRIES {
        bigint id PK
        bigint patient_id FK
        bigint service_id FK
        bigint served_by FK "nullable"
        string queue_number
        int sequence_number
        enum status "WAITING|CALLED|IN_SERVICE|COMPLETED|CANCELLED|SKIPPED"
        enum priority "NORMAL|URGENT"
        timestamp joined_at
        timestamp called_at "nullable"
        timestamp service_started_at "nullable"
        timestamp completed_at "nullable"
        timestamp cancelled_at "nullable"
        timestamp skipped_at "nullable"
        timestamps created_at
        timestamps updated_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string type
        string title
        text body
        json data "nullable"
        timestamp read_at "nullable"
        timestamps created_at
        timestamps updated_at
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK "nullable"
        string action
        string entity_type
        bigint entity_id "nullable"
        json metadata "nullable"
        string ip_address "nullable"
        timestamp created_at
    }

    USERS ||--o{ QUEUE_ENTRIES : "patient has"
    USERS ||--o{ QUEUE_ENTRIES : "staff serves"
    SERVICES ||--o{ QUEUE_ENTRIES : "categorizes"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ AUDIT_LOGS : "generates"
```

---

## 5. Sequence Diagram: Patient Joins Queue

```mermaid
sequenceDiagram
    autonumber
    actor Patient
    participant Browser as Browser
    participant Controller as PatientQueueController
    participant FormReq as StoreQueueEntryRequest
    participant Service as QueueService
    participant DB as Database

    Patient->>Browser: Navigate to /patient/services
    Browser->>Controller: GET /patient/services
    Controller->>DB: SELECT active services
    DB-->>Controller: Service list
    Controller-->>Browser: Render service selection page

    Patient->>Browser: Select service, click "Join Queue"
    Browser->>Controller: POST /patient/queue {service_id}
    Controller->>FormReq: validate(service_id)
    FormReq->>DB: Check service is active
    DB-->>FormReq: Service OK
    FormReq-->>Controller: Validated data

    Controller->>Service: join(patient, service)
    Service->>DB: BEGIN TRANSACTION
    Service->>DB: SELECT queue_entries WHERE patient=X AND service=Y AND status IN (WAITING,CALLED,IN_SERVICE) FOR UPDATE
    DB-->>Service: No duplicates found
    Service->>DB: SELECT MAX(sequence) for service today FOR UPDATE
    DB-->>Service: Max sequence = 4
    Service->>DB: INSERT queue_entry (queue_number="GC-005", sequence=5, status=WAITING)
    Service->>DB: INSERT notification (patient, "Queue joined")
    Service->>DB: INSERT audit_log (action=queue.joined)
    Service->>DB: COMMIT
    DB-->>Service: Queue entry created
    Service-->>Controller: QueueEntry entity

    Controller-->>Browser: Redirect to /patient/queue/{id}
    Browser-->>Patient: Display ticket screen (GC-005, Position: 5, Est. wait: 20 mins)
```

---

## 6. Sequence Diagram: Staff Calls Next Patient

```mermaid
sequenceDiagram
    autonumber
    actor Staff
    participant Browser as Staff Browser
    participant Controller as StaffQueueController
    participant Service as QueueService
    participant DB as Database

    Staff->>Browser: Click "Call Next Patient"
    Browser->>Controller: POST /staff/queue/call-next {service_id}
    Controller->>Service: callNext(staff, service)
    Service->>DB: BEGIN TRANSACTION
    Service->>DB: SELECT queue_entries WHERE service=X AND status=WAITING ORDER BY priority DESC, sequence ASC LIMIT 1 FOR UPDATE
    DB-->>Service: Next ticket (GC-005)
    Service->>DB: UPDATE queue_entry SET status=CALLED, called_at=NOW(), served_by=staffId
    Service->>DB: INSERT notification (patient, "You have been called")
    Service->>DB: INSERT audit_log (action=queue.called)
    Service->>DB: COMMIT
    DB-->>Service: Updated entry
    Service-->>Controller: QueueEntry
    Controller-->>Browser: JSON {success, entry}
    Browser-->>Staff: Dashboard updates to show GC-005 as CALLED
```

---

## 7. Application Architecture

```mermaid
graph TB
    subgraph Routes["routes/web.php"]
        R1["Public Routes\n/ (landing)"]
        R2["Auth Routes\n/login /register"]
        R3["Patient Routes\n/patient/*"]
        R4["Staff Routes\n/staff/*"]
        R5["Admin Routes\n/admin/*"]
        R6["API Routes\n/api/* (polling)"]
    end

    subgraph Middleware["Middleware Stack"]
        M1["Auth (authenticate)"]
        M2["RoleMiddleware\n(patient|staff|admin)"]
        M3["CSRF Protection"]
    end

    subgraph Controllers["Controllers (app/Http/Controllers)"]
        C1["Auth/* Controllers"]
        C2["Patient/DashboardController\nPatient/QueueController"]
        C3["Staff/DashboardController\nStaff/QueueController"]
        C4["Admin/DashboardController\nAdmin/ServiceController\nAdmin/UserController\nAdmin/AuditController"]
        C5["Api/QueueStatusController"]
    end

    subgraph Services["Services (app/Services)"]
        S1["QueueService\n- join()\n- callNext()\n- startService()\n- complete()\n- skip()\n- recall()\n- cancel()"]
        S2["NotificationService\n- notify()"]
        S3["AuditService\n- record()"]
    end

    subgraph Models["Eloquent Models (app/Models)"]
        MOD1["User"]
        MOD2["Service"]
        MOD3["QueueEntry"]
        MOD4["Notification"]
        MOD5["AuditLog"]
    end

    Routes --> Middleware
    Middleware --> Controllers
    Controllers --> Services
    Services --> Models
```
