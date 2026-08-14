# SaaS UI/UX Visual Standard Rules

---

## 1. Professional Visual Excellence
* **Color System**: Use curated slate/indigo/emerald/rose color tokens from Tailwind CSS. Avoid raw primary red, blue, green colors.
* **Typography**: Clean, readable sans-serif typography (`Inter`, `System-UI`). High visual contrast (`slate-900` body on `slate-50` / `white` backgrounds).
* **Card Elevation**: Subtle borders (`border border-slate-200`) and soft shadows (`shadow-sm` or `shadow-md`).

## 2. Dynamic States Requirements
Every interactive interface MUST include explicit designs for:
- **Default State**: Populated list/table/form.
- **Empty State**: Friendly visual icon and helpful text when no tickets/services exist.
- **Loading State**: Subtle skeleton loaders or disabled buttons with spinner icons during submission.
- **Error State**: Inline form validation alerts with clear error message strings.

## 3. Responsive Layout Guidelines
- Kiosk Mode: Optimized for touchscreens (large buttons, step wizard).
- TV Monitor Mode: Fullscreen high-contrast display with large font sizes (`text-4xl` / `text-6xl`).
- Staff & Admin Dashboard: High-density data tables with responsive horizontal scrolling on small screens.
