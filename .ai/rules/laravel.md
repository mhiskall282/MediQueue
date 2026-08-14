# Laravel Development Rules

Follow standard Laravel conventions and architectural best practices.

---

## 1. Controller Discipline
* **Keep Controllers Thin**: Controllers must only receive HTTP requests, trigger validation, delegate domain logic to Service/Action classes, and return responses or views.
* **Resourceful Controllers**: Use standard RESTful action names (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`).

## 2. Form Requests & Authorization
* **Form Request Validation**: NEVER perform manual `$request->validate()` inside controller methods for complex forms. Create dedicated `FormRequest` classes in `app/Http/Requests/`.
* **Policies & Gates**: Encapsulate authorization logic inside Laravel Policies (`app/Policies/`). Protect controller actions using `$this->authorize()` or middleware.

## 3. Service & Action Layer
* Move core queue calculations, ticket generation, state transitions, and analytics logic into dedicated classes in `app/Services/` or `app/Actions/`.
* Ensure state-changing domain workflows execute within `DB::transaction()`.

## 4. Eloquent & Database
* Always specify explicit `$fillable` or `$guarded` properties on Eloquent models to prevent mass assignment vulnerabilities.
* Define strict return types for Eloquent relationships (`public function tickets(): HasMany`).
* Use local query scopes for common queries (e.g. `scopeWaiting($query)`).

## 5. Routing & Assets
* Use **Named Routes** for all routes (e.g., `route('kiosk.register')`).
* Use Route Model Binding (`public function show(QueueTicket $ticket)`).
* Use Vite for frontend compilation (`@vite(['resources/css/app.css', 'resources/js/app.js'])`).
