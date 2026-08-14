# Git & Version Control Rules

---

## 1. Commit Message Convention
Follow Conventional Commits specification for all Git commits:
- `feat(scope)`: A new user-facing feature.
- `fix(scope)`: A bug fix.
- `docs(scope)`: Documentation updates or `.ai/` state updates.
- `style(scope)`: Blade/Tailwind styling changes without logic impact.
- `refactor(scope)`: Code changes that neither fix a bug nor add a feature.
- `test(scope)`: Adding or fixing automated tests.
- `chore(scope)`: Maintenance or config updates.

*Example*: `feat(queue): implement atomic ticket number generator service`

## 2. State Synchronization Rule
Whenever a major feature or bug fix is committed:
1. Update [.ai/state/current-status.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/current-status.md).
2. Update [.ai/state/completed-work.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/completed-work.md).
3. If new technical debt was introduced, log it in [.ai/state/technical-debt.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/technical-debt.md).
