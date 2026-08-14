# University Examination Context & Criteria

## 1. Assessment Overview

MediQueue is developed as an individual university examination project for Software Engineering.

**Total Marks Available**: 50 Marks

The project must present comprehensive, verifiable evidence across all 13 software engineering lifecycle phases.

---

## 2. Detailed Marking Scheme Breakdown

| Phase | Evaluation Criteria | Allocated Marks |
|---|---|:---:|
| **1. Requirements Engineering & SRS** | Comprehensive problem statement, user personas, MoSCoW functional & non-functional requirements, acceptance criteria, and traceability matrix. | **7** |
| **2. Software Effort Estimation** | Rigorous use of standard estimation models (Use Case Points or Function Point Analysis), explicit assumptions, complexity metrics, person-hour calculations, and mapping to 48-hour exam scope. | **5** |
| **3. System Analysis & Design** | Complete diagrammatic suite: Context Diagram, Use Case Diagram, Layered Architecture Diagram, ER Diagram, Sequence Diagrams (Queue Ticket Generation, Staff Ticket Processing), and UI Wireframes. | **6** |
| **4. Implementation & Functionality** | Clean, modular, type-safe Laravel implementation covering patient registration, atomic queue ticketing, live queue monitor, staff queue management dashboard, and admin service management. | **10** |
| **5. Testing & QA** | Comprehensive automated test suite (Unit, Integration, Validation, Authorization) using Pest/PHPUnit, high assertion coverage, and zero failing tests. | **5** |
| **6. Technical Debt Management** | Explicit identification, categorization, impact analysis, and resolution plan for technical debt items resulting from the 48-hour scope constraint. | **6** |
| **7. Deployment** | Production readiness, deployment configuration (Docker/Web server), environment setup, asset optimization (`npm run build`), and health-check verification. | **3** |
| **8. Documentation & User Manual** | High-quality SRS, technical documentation, inline docblocks, system architecture docs, and user guide for Patients, Staff, and Admins. | **3** |
| **9. Maintenance & Future Evolution** | Maintenance strategy, system scalability roadmap, API extension plan, and future feature breakdown. | **3** |
| **TOTAL** | | **50** |

---

## 3. Examination Principles for AI Agents

1. **Acceptance Criteria**: Every mark category represents a mandatory project acceptance criterion.
2. **Traceability**: All code implementations must trace back to functional requirements listed in the SRS (`REQ-F-001`, `REQ-F-002`, etc.).
3. **Zero Fabrication**: All estimates, diagram explanations, test results, and deployment commands must be real, reproducible, and grounded in working repository artifacts.
