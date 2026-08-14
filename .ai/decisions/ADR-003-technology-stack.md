# ADR-003: Technology Stack & Database Strategy

* **Status**: Accepted
* **Date**: 2026-08-14
* **Deciders**: Lead Software Engineering Agent & Examination Candidate

---

## Context & Problem Statement

We must establish the technology stack, runtime versions, asset compilation toolchain, and database engine for MediQueue to ensure environmental stability across development, testing, and production environments.

---

## Decision Outcome

1. **Language & Framework**: PHP 8.2+ with Laravel 10/11.
2. **Frontend UI Engine**: Blade Templates + Tailwind CSS v3/v4 + Vite + Vanilla JS / Alpine.js.
3. **Database Strategy**:
   - **Development & Automated Testing**: SQLite (`database.sqlite` / `:memory:`) for instant execution without local database server dependencies.
   - **Production Readiness**: Eloquent ORM migrations designed with strict SQL standards compatible with MySQL 8.0+ / PostgreSQL 15+.
4. **Tooling & Environment**:
   - PHP CLI: `C:\xampp\php\php.exe`
   - Composer: `C:\xampp\php\composer.phar`
   - Node.js: `v22.11.0` / `npm 11.6.2`

---

## Consequences

* Zero database setup friction for local automated test suites.
* High execution speed for test runner (`RefreshDatabase` on SQLite in-memory runs in milliseconds).
* Easy migration path to MySQL/PostgreSQL for production deployment.
