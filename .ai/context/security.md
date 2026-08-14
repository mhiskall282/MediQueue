# Security Context & Data Standards

## 1. Security Architecture & Threat Profile

MediQueue operates in a healthcare environment where patient privacy, system integrity, and role boundaries are paramount.

---

## 2. Security Controls & Standards

1. **Authentication**:
   - Secure password hashing using Argon2id / bcrypt.
   - Session-based web authentication with HTTP-only, SameSite cookies.

2. **Role-Based Access Control (RBAC)**:
   - Enforced using Laravel Policies and Middleware.
   - Roles: `admin`, `doctor`, `nurse`, `receptionist`, `patient` (guest / public ticket token).

3. **Input Validation & Sanitization**:
   - 100% of HTTP inputs parsed through Laravel Form Requests.
   - Strict field length constraints, phone format validation, and string escaping.

4. **Database Protection**:
   - Mass-assignment protection via explicit `$fillable` arrays on Eloquent Models.
   - SQL Injection protection via PDO bound parameters.

5. **Cross-Site Scripting (XSS) & CSRF**:
   - All Blade templates must use `{{ }}` auto-escaping.
   - All state-modifying POST/PUT/DELETE forms must include `@csrf` token.

6. **Audit Trail**:
   - Administrative and ticket status changes logged to `audit_logs` table.

---

## 3. Strict Non-Scope & Privacy Safeguard

* **No Medical Health Records (PHI/EMR)**: MediQueue stores basic contact info for queue identification ONLY. No medical diagnostic notes, clinical history, or prescription data are stored.
* **No Hardcoded Secrets**: Secrets (database passwords, app keys) must reside exclusively in `.env`.
