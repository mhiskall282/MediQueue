# Testing Context & QA Strategy

## 1. Testing Framework & Philosophy

MediQueue uses **Pest PHP / PHPUnit** for automated testing.

The test suite must validate:
1. **Domain Logic Accuracy**: Ticket sequence generation, wait time algorithms, status transitions.
2. **Data Integrity**: Concurrency protection, duplicate active ticket prevention.
3. **HTTP & Authorization**: Route accessibility, form validation errors, role-based protection.

---

## 2. Test Suite Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php
│   │   └── RoleAuthorizationTest.php
│   ├── Patient/
│   │   ├── KioskRegistrationTest.php
│   │   └── QueueStatusMonitorTest.php
│   ├── Staff/
│   │   ├── TicketCallingTest.php
│   │   └── TicketStatusTransitionTest.php
│   └── Admin/
│       └── ServiceManagementTest.php
└── Unit/
    ├── QueueTicketNumberGeneratorTest.php
    └── WaitTimeEstimatorTest.php
```

---

## 3. Mandatory Test Scenarios

1. **Patient Ticket Generation**:
   - `test_patient_can_register_and_receive_unique_ticket_number()`
   - `test_prevents_duplicate_active_ticket_for_same_patient_and_service()`
   - `test_ticket_sequence_resets_daily()`

2. **Staff Workflow**:
   - `test_staff_can_call_next_patient_in_queue()`
   - `test_staff_cannot_call_patient_from_unassigned_service()`
   - `test_validates_state_transitions_called_to_serving_to_completed()`

3. **Security & Authorization**:
   - `test_unauthenticated_user_cannot_access_staff_dashboard()`
   - `test_receptionist_cannot_modify_admin_services()`
