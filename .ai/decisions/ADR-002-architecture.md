# ADR-002: Monolithic Laravel & Service Layer Architecture

* **Status**: Accepted
* **Date**: 2026-08-14
* **Deciders**: Lead Software Engineering Agent & Examination Candidate

---

## Context & Problem Statement

We need an architectural pattern for MediQueue that ensures clean separation of concerns, high testability, rapid UI rendering, and minimal deployment overhead during the 48-hour exam window.

---

## Considered Options

1. **Option A: Decoupled SPA Architecture** (React/Vue Single Page Application + Laravel REST API).
2. **Option B: Layered Monolithic Architecture** (Laravel 10/11 + Blade Components + Service/Action Layer + Tailwind CSS).
3. **Option C: Microservices Architecture** (Separate services for Ticket Generator, User Auth, Display Service).

---

## Decision Outcome

**Chosen Option**: **Option B (Layered Monolithic Architecture)**.

### Rationale:
- **Development Velocity**: Laravel + Blade allows server-side rendering with zero API serialization boilerplate or SPA state hydration bugs.
- **Maintainability**: Moving core domain logic (ticket allocation, queue state shifts) into dedicated Service classes (`app/Services/`) keeps Controllers thin and allows isolated unit testing.
- **Simplicity**: Single deployment artifact without needing CORS setup, multiple build pipelines, or separate API gateways.

---

## Consequences

* **Positive**:
  - Extremely fast dev-test feedback loop.
  - Simplified deployment pipeline.
  - High test coverage achievable via standard PHPUnit / Pest tests.
* **Negative / Technical Debt**:
  - Mobile apps would require introducing API endpoints in the future (mitigated by Laravel's built-in API route support).
