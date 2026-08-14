# Bug Fixing Workflow

Follow this procedure when diagnosing, reproducing, and fixing defects in MediQueue.

---

## Workflow Steps

1. **Read Log Output**: Never form diagnostic hypotheses without inspecting full, un-truncated error stack traces (`storage/logs/laravel.log` or test CLI logs).
2. **Reproduce via Test Case**: Write a failing automated test case in `tests/Feature/` or `tests/Unit/` that reliably reproduces the bug.
3. **Trace Upstream Cause**: Identify the root cause in the Service, Controller, or Migration layer rather than masking symptoms with silent fallbacks.
4. **Implement Root Cause Fix**: Modify the underlying logic while preserving existing API contracts.
5. **Verify Test Pass**: Run `php artisan test` to confirm the reproduction test passes and no regressions were introduced.
6. **Log Bug Status**: Update [.ai/state/known-issues.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/known-issues.md).
