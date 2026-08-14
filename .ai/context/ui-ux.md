# UI/UX Context & Design System Standard

## 1. Aesthetic Direction: Premium Healthcare SaaS

MediQueue must look like a high-end, commercial medical software application (e.g. Epic Systems, Cerner, or modern health tech SaaS platforms).

### Key Visual Characteristics:
- **Clean, Clinical Palette**: Slate grey background (`bg-slate-50`), crisp white card containers (`bg-white`), dark slate typography (`text-slate-900`), and clinical indigo/emerald accents (`bg-indigo-600`, `bg-emerald-600`).
- **Clear Information Hierarchy**: Bold, large typography for queue numbers (e.g. **GC-014** in `text-4xl font-extrabold tracking-tight`).
- **High Contrast Status Badges**:
  - `WAITING`: Soft Amber (`bg-amber-100 text-amber-800 border border-amber-300`)
  - `CALLED`: Pulsing Blue/Indigo (`bg-indigo-100 text-indigo-800 animate-pulse`)
  - `SERVING`: Emerald Green (`bg-emerald-100 text-emerald-800`)
  - `COMPLETED`: Slate Grey (`bg-slate-100 text-slate-600`)
  - `NO-SHOW` / `CANCELLED`: Rose Red (`bg-rose-100 text-rose-800`)
  - `EMERGENCY`: Deep Red / Flashing Accent (`bg-red-600 text-white font-bold`)

---

## 2. Dedicated View Modes

1. **Patient Self-Registration Kiosk (`/kiosk`)**:
   - Touchscreen-friendly layout with large touch targets (`min-h-[64px]`).
   - 3-step simple wizard: Select Service -> Enter Basic Details -> Issue Digital Ticket.
   - Screen print / digital QR token view.

2. **Public Waiting Room TV Display (`/display`)**:
   - Fullscreen dark-mode or high-visibility light-mode display (`text-5xl`).
   - Audio ping chime effect on new patient call.
   - Dual panel: "NOW SERVING" (Current calls with room number) & "UP NEXT" (Waiting list).

3. **Staff Consultation Dashboard (`/staff/queue`)**:
   - Desktop optimized, high-density layout.
   - Action controls: **Call Next**, **Recall**, **Start Consultation**, **Mark Complete**, **Mark No-Show**, **Transfer Queue**.

4. **Admin Service & User Panel (`/admin`)**:
   - Analytics cards (Total Patients Today, Avg Waiting Time, Active Counters).
   - Service management table with inline status toggles.

---

## 3. Micro-Interactions & Usability

* **Live Auto-Refresh**: Auto-polling / SSE for live queue updates every 5 seconds on TV display and patient monitor.
* **Empty States**: Clear, reassuring visual empty states (e.g. "No patients currently waiting for General Consultation.").
* **Error Handling**: Inline validation errors with accessible `aria-invalid` attributes and red helper text.
