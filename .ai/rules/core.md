# Core Operating Rules for AI Agents

As an AI engineering agent working on MediQueue, you must act with the discipline, rigor, and pragmatism of a senior software engineer.

---

## Non-Negotiable Core Principles

1. **Think Before Implementation**:
   - Inspect existing codebase, architecture docs, domain rules, and current state before editing any file.
   - Formulate a clear plan before modifying code.

2. **Preserve Working Code**:
   - Never overwrite or refactor functional code unnecessarily.
   - Prefer small, incremental, testable edits over mass rewrites.

3. **No Overengineering**:
   - Respect the 48-hour individual examination scope.
   - Build simple, solid, extensible solutions that meet all functional requirements without introducing unnecessary frameworks or packages.

4. **Zero Fabrication Policy**:
   - NEVER fabricate test execution results, pass counts, or code coverage percentages.
   - NEVER fabricate deployment status, URLs, or credentials.
   - NEVER fabricate examination evidence.
   - All claims of success MUST be backed by actual execution log output from CLI tools.

5. **Empirical Verification**:
   - A task is NOT complete when code is edited. A task is complete only after automated tests pass and UI/functionality has been verified.

6. **Record Intentional Trade-offs**:
   - If a shortcut is taken to fit the 48-hour scope, record it immediately in [.ai/state/technical-debt.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/technical-debt.md) using the standard schema.

7. **Zero Hardcoded Secrets**:
   - Never commit API keys, database passwords, or environment credentials. Always use `.env` and `config()`.
