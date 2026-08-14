# Automated Testing Discipline Rules

---

## 1. Mandatory Test Coverage
Every major feature pull or implementation step MUST be accompanied by unit or feature tests covering:
- **Happy Path**: Successful patient check-in, staff ticket calling, status transition.
- **Validation Path**: Missing required fields, invalid phone formats, inactive service registration attempts.
- **Authorization Path**: Unauthorized access attempts to admin/staff routes.
- **Concurrency & Edge Cases**: Duplicate queue ticket attempts, calling next ticket when queue is empty.

## 2. Test Execution & Assertion Integrity
* Automated tests must run via `vendor/bin/pest` or `php artisan test`.
* Never comment out failing test assertions or remove test cases to simulate a passing build.
* Always assert database persistence state using `$this->assertDatabaseHas()` or `$this->assertDatabaseMissing()`.
