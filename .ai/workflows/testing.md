# QA & Testing Workflow

Follow this procedure when running test suites and verifying MediQueue functionality.

---

## Workflow Steps

1. **Prepare Test Database**: Ensure `.env.testing` or SQLite in-memory test database configuration is active.
2. **Execute Full Suite**:
   ```bash
   php artisan test
   ```
3. **Execute Filtered Suite for Specific Module**:
   ```bash
   php artisan test --filter QueueTicketTest
   ```
4. **Inspect Coverage & Failure Logs**: If any test fails, inspect the log output immediately.
5. **Verify Assertions**: Ensure tests verify database persistence (`assertDatabaseHas`), HTTP response codes, session errors, and redirect locations.
6. **Update Test Summary**: Update [.ai/state/current-status.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/current-status.md) with current test pass count.
