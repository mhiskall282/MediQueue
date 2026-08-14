# Database & Schema Discipline Rules

---

## 1. Migration Discipline
* All database changes MUST be executed via version-controlled Laravel migrations. Never modify database schemas manually.
* Ensure all migration `up()` methods are paired with reversible `down()` methods.
* Apply explicit foreign key constraints with appropriate cascade rules (`onDelete('cascade')` or `onDelete('restrict')`).

## 2. Concurrency & Transaction Integrity
* Operations affecting queue position or ticket allocation MUST be wrapped inside `DB::transaction()`.
* Use pessimistic locking (`lockForUpdate()`) when fetching sequence counters during concurrent ticket creation.

## 3. Seeders & Testing Data
* Maintain deterministic factories (`database/factories/`) and seeders (`database/seeders/`).
* Running `php artisan migrate:fresh --seed` MUST leave the system in a fully working state with ready-to-test accounts and sample queue data.
