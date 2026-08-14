# Blade View & Component Rules

Adhere to the following rules when building user interfaces in Blade.

---

## 1. Modular Component Architecture
* **Avoid Monolithic Templates**: Split views into modular, reusable Blade components (`resources/views/components/`).
* **Core Components Required**:
  - `<x-layout>`: Base HTML wrapper with header, container, flash alerts, and footer.
  - `<x-card>`: Container with clean border, padding, and shadow.
  - `<x-badge>`: Dynamic color-coded queue status indicator.
  - `<x-button>`: Accessible button supporting primary, secondary, danger, and outline variants.
  - `<x-modal>`: Accessible dialog component.
  - `<x-queue-table>`: Styled table component with responsive overflow wrapper.

## 2. Layout & Semantic HTML
* Use semantic HTML5 markup (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`).
* Maintain clean layout inheritance (`x-layout` component or `@extends('layouts.app')`).

## 3. Accessibility & Responsive Design
* **Focus States**: Ensure all interactive elements have visible focus indicators (`focus:ring-2 focus:ring-indigo-500`).
* **ARIA Attributes**: Provide appropriate `aria-label`, `aria-live` (for live queue displays), and `aria-expanded` attributes.
* **Mobile Responsiveness**: Test views at mobile viewport (`375px`), tablet (`768px`), and desktop (`1280px`).
