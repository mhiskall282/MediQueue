---
name: system-design
description: Architectural, sequence, entity-relationship (ERD), and component design specifications for MediQueue using Mermaid.js.
---

# System Design Skill Guide

Use this skill when designing architectural components, database schemas, sequence diagrams, and UI structures for **MediQueue**.

---

## 1. Mermaid Diagram Standards

All system design diagrams MUST be expressed in valid Mermaid markdown syntax to ensure readability in web browsers and repository previewers.

---

## 2. Mandatory Core System Diagrams

### Diagram 1: Context Diagram (Level 0 DFD)

```mermaid
graph TD
    Patient["Patient / Walk-In"] -->|Select Service & Register| System[("MediQueue System")]
    System -->|Issue Queue Ticket & Status| Patient
    
    Staff["Healthcare Staff / Doctor"] -->|Call Next / Update Status| System
    System -->|Display Patient Details & Queue| Staff
    
    Display["TV Waiting Room Display"] <--|Live Queue Poll / SSE| System
    
    Admin["System Administrator"] -->|Manage Services & Staff| System
```

---

### Diagram 2: Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ AUDIT_LOGS : generates
    SERVICES ||--o{ ROOMS : operates
    SERVICES ||--o{ QUEUE_TICKETS : categorizes
    PATIENTS ||--o{ QUEUE_TICKETS : holds
    ROOMS ||--o{ QUEUE_TICKETS : processes
    USERS ||--o{ QUEUE_TICKETS : serves

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role
    }

    SERVICES {
        bigint id PK
        string name
        string code UK
        int avg_duration_minutes
        boolean is_active
    }

    ROOMS {
        bigint id PK
        string room_number
        string name
        bigint service_id FK
        boolean is_active
    }

    PATIENTS {
        bigint id PK
        string patient_code UK
        string name
        string phone
        string national_id
    }

    QUEUE_TICKETS {
        bigint id PK
        string ticket_number
        int sequence_number
        bigint patient_id FK
        bigint service_id FK
        bigint room_id FK
        bigint staff_id FK
        enum status
        enum priority
        timestamp called_at
        timestamp completed_at
    }
```

---

### Diagram 3: Sequence Diagram (Atomic Ticket Generation)

```mermaid
sequenceDiagram
    autonumber
    actor Patient
    participant Kiosk as Kiosk View / Controller
    participant Service as QueueTicketService
    participant DB as Relational Database

    Patient->>Kiosk: Selects Service (e.g. General Consultation) & Enters Name/Phone
    Kiosk->>Service: issueTicket(patientData, serviceId)
    Service->>DB: BEGIN TRANSACTION
    Service->>DB: SELECT * FROM queue_tickets WHERE service_id = X AND created_at = TODAY FOR UPDATE
    DB-->>Service: Current Max Sequence (e.g., 13)
    Service->>Service: Compute Next Ticket Number (GC-014)
    Service->>DB: INSERT INTO queue_tickets (ticket_number: "GC-014", sequence: 14, status: "WAITING")
    Service->>DB: COMMIT TRANSACTION
    DB-->>Service: Saved Ticket Record
    Service-->>Kiosk: QueueTicket Entity
    Kiosk-->>Patient: Render Digital Ticket Screen (GC-014, Est Wait: 15 mins)
```

---

### Diagram 4: Sequence Diagram (Staff Ticket Calling Flow)

```mermaid
sequenceDiagram
    autonumber
    actor Staff
    participant Dashboard as Staff Controller
    participant Service as QueueManagementService
    participant DB as Relational Database

    Staff->>Dashboard: Click "Call Next Patient" (Room 102)
    Dashboard->>Service: callNextTicket(staffId, roomId, serviceId)
    Service->>DB: BEGIN TRANSACTION
    Service->>DB: SELECT * FROM queue_tickets WHERE service_id = X AND status = 'WAITING' ORDER BY priority DESC, sequence_number ASC LIMIT 1 FOR UPDATE
    DB-->>Service: Ticket Entity (GC-014)
    Service->>DB: UPDATE queue_tickets SET status = 'CALLED', room_id = 102, staff_id = staffId, called_at = NOW() WHERE id = GC-014
    Service->>DB: COMMIT TRANSACTION
    Service-->>Dashboard: Updated Ticket Status
    Dashboard-->>Staff: Render Patient Details (Ticket GC-014)
```
