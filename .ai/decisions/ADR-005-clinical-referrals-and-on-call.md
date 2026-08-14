# ADR-005: Diagnostic Laboratory Referral Loops, On-Call Rostering, and Code Red Trauma Admissions

## Status
Accepted

## Context
In healthcare facilities, patient care is rarely a single linear interaction. Doctors frequently refer patients for diagnostic blood tests or radiological imaging, requiring patients to return to the original clinician for final review without losing their place in the care hierarchy. Additionally, emergency trauma victims (unconscious John/Jane Doe patients) require rapid intake without verified identification.

## Decision
1. **Clinical Workflow Stages**: Introduce `clinical_workflow_stage` (`INITIAL_TRIAGE`, `IN_CONSULTATION`, `SENT_TO_LAB`, `LAB_COMPLETED`, `RETURNED_FOR_REVIEW`, `DISCHARGED`).
2. **Automated Lab Loopback Engine**: When lab technicians record results, the ticket automatically transitions back to the referring doctor's queue with retained `ORANGE` acuity and `URGENT` priority.
3. **Emergency Trauma Protocol**: Implement rapid intake creating a temporary Trauma MRN (`EMG-DOE-XXXX`), auto-assigning `RED` triage, allocating a triage bay, and dispatching a Code Red emergency page to active on-call doctors.
4. **On-Call Duty Rosters**: Track clinician shifts (`DAY`, `NIGHT`, `ON_CALL_TRAUMA`, `ICU_COVER`) with single-click emergency clinical paging.

## Consequences
- Seamless patient movement between doctors and diagnostic laboratories without administrative friction.
- Life-saving rapid admission protocols for unconscious trauma patients.
