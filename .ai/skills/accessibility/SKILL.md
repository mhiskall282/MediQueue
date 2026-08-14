---
name: accessibility
description: Accessibility (WCAG 2.1 AA), keyboard navigation, focus management, screen reader support, and color contrast rules for MediQueue.
---

# Accessibility (a11y) Skill Guide

Use this skill when auditing or implementing UI components for **MediQueue** to ensure compliance with WCAG 2.1 AA standards.

---

## 1. Key Accessibility Guidelines for Healthcare UI

1. **Keyboard Navigation**:
   - Every interactive element (buttons, kiosk service cards, form inputs) must be operable using the `Tab`, `Enter`, and `Space` keys alone.
   - Never remove default focus outlines without providing a high-visibility replacement (`focus:ring-2 focus:ring-indigo-500`).

2. **Color Contrast (WCAG AA)**:
   - Text elements must maintain a contrast ratio of at least `4.5:1` against their background.
   - Status badges must rely on both text labels AND icons/colors (never use color alone to convey status).

3. **Screen Reader Support**:
   - Form controls must have associated `<label>` tags with matching `for` attributes.
   - Live queue status displays must use `aria-live="polite"` so screen readers announce queue updates automatically.

4. **Touch Target Size**:
   - Kiosk interface buttons must have a minimum interactive touch area of `48px x 48px` to support elderly or visually impaired clinic attendees.
