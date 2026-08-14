# MediQueue — AI Agent Environment Guide

Welcome to the **MediQueue** Agentic Development Environment (`.ai/`).

This directory turns this repository into a structured, context-aware, rule-driven environment designed for AI coding agents. The primary objective is to guide AI agents to behave like disciplined senior software engineers rather than blindly generating code.

> **CRITICAL MANDATE FOR ALL AI AGENTS:**  
> **Never begin substantial implementation without first reading the relevant `.ai/context/`, `.ai/rules/`, and `.ai/skills/` files.**

---

## 1. Structure of `.ai/`

```
.ai/
├── README.md                 # Entry point & operating instructions (this file)
├── context/                  # Project domain, exam expectations, & architecture details
│   ├── project.md            # High-level product overview & scope
│   ├── examination.md        # University evaluation metrics (50 marks breakdown)
│   ├── architecture.md       # Layered monolith architecture & component boundary rules
│   ├── domain.md             # Healthcare clinic domain entities, workflows, & state transitions
│   ├── database.md           # Schema strategy, atomic ticket generation, indexing rules
│   ├── ui-ux.md              # SaaS-grade healthcare design system & component rules
│   ├── deployment.md         # Production build & environment configuration setup
│   ├── testing.md            # Pest/PHPUnit test strategy & required coverage metrics
│   └── security.md           # Authentication, RBAC, & data protection standards
├── rules/                    # Non-negotiable coding and operating constraints
│   ├── core.md               # Senior engineering behavior & zero-fabrication rules
│   ├── laravel.md            # Eloquent, Form Requests, Policies, & Controller discipline
│   ├── blade.md              # Reusable UI components & semantic HTML layout rules
│   ├── database.md           # Transactional integrity & schema migration discipline
│   ├── security.md           # Auth, RBAC, CSRF, XSS, & audit logging rules
│   ├── testing.md            # Automated test suite requirements
│   ├── ui.md                 # SaaS UI standards & aesthetic requirements
│   ├── git.md                # Git commit conventions & state tracking
│   └── documentation.md      # Inline comments, SRS, & User Manual maintenance rules
├── skills/                   # Domain & engineering skill guides (with YAML frontmatter)
│   ├── requirements-engineering/SKILL.md
│   ├── software-estimation/SKILL.md
│   ├── system-analysis/SKILL.md
│   ├── system-design/SKILL.md
│   ├── laravel-development/SKILL.md
│   ├── blade-ui/SKILL.md
│   ├── database-design/SKILL.md
│   ├── testing/SKILL.md
│   ├── technical-debt/SKILL.md
│   ├── deployment/SKILL.md
│   ├── documentation/SKILL.md
│   └── accessibility/SKILL.md
├── workflows/                # Step-by-step procedures for common tasks
│   ├── feature-development.md
│   ├── bug-fixing.md
│   ├── testing.md
│   ├── deployment.md
│   ├── technical-debt.md
│   └── examination-review.md
├── decisions/                # Architectural Decision Records (ADRs)
│   ├── ADR-001-project-scope.md
│   ├── ADR-002-architecture.md
│   └── ADR-003-technology-stack.md
├── state/                    # Live state tracking of project progress
│   ├── current-status.md
│   ├── completed-work.md
│   ├── known-issues.md
│   ├── technical-debt.md
│   └── next-tasks.md
└── templates/                # Standardized document templates
    ├── feature-spec.md
    ├── test-plan.md
    ├── ADR.md
    └── technical-debt-item.md
```

---

## 2. Agent Protocol & Workflow

Before undertaking any task, an agent MUST execute the following sequence:

### Step 1: Context Discovery & Skill Activation
1. Read [.ai/state/current-status.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/current-status.md) and [.ai/state/next-tasks.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/next-tasks.md) to understand current progress.
2. Read the relevant context files in [.ai/context/](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/context/) corresponding to your task (e.g., `domain.md` for queue logic, `security.md` for RBAC).
3. Activate the matching skill in `.ai/skills/<skill-name>/SKILL.md` by loading its instructions.
4. Read applicable rules in [.ai/rules/](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/rules/).

### Step 2: Implementation & Verification
1. Follow the corresponding workflow in `.ai/workflows/` (e.g., `feature-development.md`).
2. Write clean, documented, type-safe Laravel code adhering to `.ai/rules/laravel.md` and `.ai/rules/blade.md`.
3. Run automated unit and integration tests using PHPUnit / Pest. Never claim a feature works without empirical test execution results.

### Step 3: State Maintenance & Documentation
1. Update [.ai/state/current-status.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/current-status.md) and [.ai/state/completed-work.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/completed-work.md).
2. Record any intentional trade-offs or technical debt introduced in [.ai/state/technical-debt.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/technical-debt.md).
3. If an architectural decision was made, draft a new ADR in `.ai/decisions/` using [.ai/templates/ADR.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/templates/ADR.md).

---

## 3. Mandatory Requirements & Constraints

* **48-Hour Exam Scope**: All designs and implementations must respect the 48-hour scope constraint. Avoid over-engineering while maintaining software engineering rigor.
* **No Medical Diagnosis Logic**: MediQueue is strictly an administrative clinic queue management system (patient check-in, queue positions, wait-time estimation, staff ticket calling, service configuration). It does NOT process medical records or clinical diagnostic logic.
* **Empirical Verification**: All test runs, build steps, and deployments must be actually executed via tool commands. Never fake log output or test pass counts.
