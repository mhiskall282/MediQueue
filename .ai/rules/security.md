# Security Rules & Data Protection Standards

---

## 1. Authentication & Authorization
* Protect all non-public routes with appropriate middleware (`auth`, `role:admin`, `role:doctor`).
* Check authorization policies on every mutating action.
* Enforce secure password validation rules (`min:8`, mixed case, numbers).

## 2. Injection & XSS Safeguards
* Never use raw SQL queries with unescaped string concatenation. Use Eloquent bindings or `DB::raw()` with parameter arrays.
* HTML output in Blade must always be rendered using `{{ }}` unless explicitly sanitized.
* Apply CSRF protection (`@csrf`) to all POST, PUT, PATCH, and DELETE forms.

## 3. Secret & Credential Safety
* NEVER commit `.env` files, production keys, passwords, or secret tokens into source control.
* Use environment variable lookups via `config('services.name')`.
* Log audit entries for administrative role changes, service updates, and emergency queue resets.
