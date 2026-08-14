# Examination Readiness Audit Workflow

Use this workflow to conduct a full assessment review against the 50-mark university grading rubric before final project submission.

---

## 12-Point Examination Checklist

| # | Evaluation Criteria | Allocated Marks | Verification Artifact / Evidence | Status |
|---|---|:---:|---|:---:|
| 1 | **Requirements Engineering & SRS** | 7 | `docs/01-REQUIREMENTS-SPECIFICATION-SRS.md` with MoSCoW priorities & RTM table. | [ ] |
| 2 | **Software Effort Estimation** | 5 | `docs/02-SOFTWARE-ESTIMATION.md` with UCP formula, weights, & 48-hour mapping. | [ ] |
| 3 | **System Analysis & Design** | 6 | `docs/03-SYSTEM-ANALYSIS-AND-DESIGN.md` with Mermaid diagrams (Context, ERD, Sequence). | [ ] |
| 4 | **Implementation & Functionality** | 10 | Working Laravel codebase (Kiosk, TV Display, Staff Dashboard, Admin Panel). | [ ] |
| 5 | **Testing & QA** | 5 | `docs/05-TESTING-AND-QA-REPORT.md` + 100% passing Pest/PHPUnit automated test suite. | [ ] |
| 6 | **Technical Debt Management** | 6 | `docs/06-TECHNICAL-DEBT-LOG.md` + [.ai/state/technical-debt.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/technical-debt.md). | [ ] |
| 7 | **Deployment** | 3 | `docs/07-DEPLOYMENT-GUIDE.md` + environment config + `/health` route response. | [ ] |
| 8 | **Documentation & User Manual** | 3 | `docs/08-USER-MANUAL.md` covering Patient, Staff, and Admin workflows. | [ ] |
| 9 | **Maintenance & Future Evolution** | 3 | `docs/09-MAINTENANCE-AND-EVOLUTION.md` post-exam roadmap. | [ ] |
| **TOTAL** | | **50 Marks** | **Comprehensive Examination Evidence** | [ ] |

---

## Audit Protocol

1. Perform a complete walkthrough of all 12 evaluation checklist items above.
2. Verify that corresponding physical documentation files exist in `docs/` and code artifacts exist in `app/`, `resources/`, and `tests/`.
3. If any evidence is missing or incomplete, flag it in [.ai/state/known-issues.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/known-issues.md) and resolve it before declaring examination readiness.
