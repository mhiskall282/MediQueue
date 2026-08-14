# Feature Development Workflow

Follow this 12-step lifecycle when developing any feature for MediQueue.

---

## 12-Step Feature Lifecycle

```
[ 1. Understand Req ] -> [ 2. Inspect Code ] -> [ 3. Identify Arch ] -> [ 4. Define Acceptance ]
                                                                                   |
[ 8. Inspect UI ]     <- [ 7. Run Tests ]    <- [ 6. Static Check ]  <- [ 5. Implement ]
       |
       v
[ 9. Fix Defects ]    -> [ 10. Update Docs ] -> [ 11. Update State ] -> [ 12. Record Debt ]
```

---

### Phase 1: Planning & Analysis
1. **Understand Requirement**: Read the feature description in `.ai/context/domain.md` or SRS and identify the target user story.
2. **Inspect Existing Code**: Search the repository using grep/file view tools to check if related models, routes, or components already exist.
3. **Identify Affected Architecture**: Determine affected layers (Database Migration -> Model -> Form Request -> Service -> Controller -> Blade View).
4. **Define Acceptance Criteria**: Establish quantifiable criteria for success.

### Phase 2: Implementation & Verification
5. **Implement Feature**: Write clean, modular, type-safe Laravel code adhering to `.ai/rules/laravel.md` and `.ai/rules/blade.md`.
6. **Run Static Checks**: Verify code syntax and formatting (`php artisan lint` or code review).
7. **Run Relevant Tests**: Execute automated tests (`php artisan test --filter FeatureNameTest`). Never skip failing assertions.
8. **Inspect UI**: Check Blade views in browser / kiosk layout to ensure SaaS visual standards, responsive layout, and contrast compliance.
9. **Fix Defects**: Address any discovered regressions or boundary bugs immediately.

### Phase 3: Documentation & State Maintenance
10. **Update Documentation**: Update inline PHPDoc comments, SRS traceability matrix, or User Manual.
11. **Update Project State**: Update [.ai/state/current-status.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/current-status.md) and [.ai/state/completed-work.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/completed-work.md).
12. **Record Technical Debt**: If trade-offs were made to meet the 48-hour exam deadline, record them in [.ai/state/technical-debt.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/technical-debt.md).
