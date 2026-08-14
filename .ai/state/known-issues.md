# Known Issues Registry

This file tracks active bugs, environment notices, and temporary repository constraints.

---

## Active Environment & Repository Issues

### [ISSUE-001] Windows Host PHP & Composer Binary Path Requirement
* **Category**: Environment Setup
* **Severity**: Low / Information
* **Description**: PHP and Composer are not registered in the global Windows system `%PATH%` environment variable, but exist at `C:\xampp\php\php.exe` and `C:\xampp\php\composer.phar`.
* **Impact**: CLI commands invoked by AI agents or users must explicitly reference `C:\xampp\php\php.exe` and `C:\xampp\php\composer.phar` when running `artisan`, `composer`, or test suites.
* **Status**: Logged & Documented in `.ai/context/deployment.md`.
