# DESIGN_SYSTEM.md

## PURPOSE

This file is the permanent source of truth for all UI generation across the project.

All future AI-generated UI/components/pages MUST follow these rules strictly.

The goal is:
- consistency
- scalability
- maintainability
- reusable architecture
- unified visual language

DO NOT violate these rules unless explicitly instructed.

---

# CORE PRINCIPLES

- Preserve existing visual identity
- Preserve existing UX behavior
- Prefer reusable components over inline UI
- Prefer semantic tokens over hardcoded values
- Prefer consistency over creativity
- Avoid duplicate UI patterns
- Avoid page-specific styling
- Maintain responsive behavior
- Maintain accessibility

---

# COMPONENT RULES

## ALWAYS USE EXISTING COMPONENTS

Use:
- `<x-ui.button />`
- `<x-ui.input />`
- `<x-ui.select />`
- `<x-ui.textarea />`
- `<x-ui.card />`
- `<x-ui.modal />`
- `<x-ui.badge />`
- `<x-ui.table />`

DO NOT:
- create duplicate components
- recreate buttons manually
- use inline Tailwind repeatedly
- hardcode styles in pages

---

# BUTTON RULES

## Allowed Variants

- primary
- secondary
- success
- danger
- warning
- ghost
- outline

## Allowed Sizes

- sm
- md
- lg

## Rules

- buttons must have consistent height
- buttons must use centralized spacing
- buttons must use centralized typography
- buttons must support loading state
- buttons must support disabled state

DO NOT:
- use random colors
- use custom padding
- use arbitrary heights

---

# INPUT RULES

Inputs must:
- have consistent height
- have consistent border radius
- have consistent focus styles
- use shared validation states
- use shared spacing

DO NOT:
- create page-specific inputs
- use inline borders
- use custom focus colors

---

# CARD RULES

Cards must:
- use shared padding
- use shared border radius
- use shared shadow
- use consistent spacing between sections

DO NOT:
- create custom card styles per page
- use inconsistent shadows
- use random spacing

---

# TABLE RULES

Tables must:
- use shared table component
- use consistent row height
- use consistent header style
- use responsive overflow handling
- support empty states
- support loading states

DO NOT:
- create raw HTML tables repeatedly
- use inconsistent padding
- use different typography styles

---

# MODAL RULES

Modals must:
- use shared modal wrapper
- use consistent width
- use shared overlay
- support mobile responsiveness
- support keyboard accessibility

DO NOT:
- create page-specific modal implementations

---

# TYPOGRAPHY RULES

## Heading Hierarchy

- h1 → page title
- h2 → section title
- h3 → subsection
- body → default text
- small → helper text

## Rules

- typography must use centralized tokens
- maintain consistent line height
- maintain consistent font weights

DO NOT:
- hardcode font sizes
- mix typography scales randomly

---

# SPACING RULES

Use ONLY spacing scale:

- 4
- 8
- 12
- 16
- 24
- 32
- 48
- 64

Rules:
- use consistent vertical rhythm
- use shared section spacing
- use shared form spacing

DO NOT:
- use arbitrary spacing
- use random margins/paddings

---

# COLOR RULES

Use semantic colors only:

- primary
- secondary
- success
- danger
- warning
- info
- background
- surface
- border
- muted
- foreground

DO NOT:
- hardcode hex colors
- use random Tailwind colors
- use inline color styling

---

# LAYOUT RULES

Use shared layouts only:

- AppLayout
- DashboardLayout
- AuthLayout
- FormLayout

Layouts must:
- maintain consistent page width
- maintain consistent padding
- maintain consistent responsive behavior

DO NOT:
- create page-level layout systems
- use inconsistent container widths

---

# FORM RULES

Forms must:
- use shared form components
- use shared validation UI
- use consistent spacing
- use shared label styles
- use shared error styles

DO NOT:
- manually style forms repeatedly

---

# RESPONSIVE RULES

All UI must:
- support mobile
- support tablet
- support desktop

Use:
- consistent breakpoints
- shared grid rules
- shared responsive spacing

DO NOT:
- create inconsistent responsive behaviors

---

# ACCESSIBILITY RULES

All components must:
- support keyboard navigation
- support focus states
- support screen readers
- use semantic HTML
- support disabled states

DO NOT:
- remove outlines without replacement
- use inaccessible color contrast

---

# TAILWIND RULES

Allowed:
- reusable utility composition
- CVA variants
- semantic utility wrappers

DO NOT:
- repeat long Tailwind chains
- use arbitrary values excessively
- hardcode colors repeatedly

Preferred:
- centralized utility patterns
- reusable class groups

---

# AI GENERATION RULES

When generating new UI:

ALWAYS:
- reuse existing components
- reuse existing layouts
- reuse existing tokens
- preserve spacing hierarchy
- preserve typography hierarchy
- preserve responsiveness
- preserve accessibility
- preserve visual consistency

NEVER:
- invent new design patterns
- redesign existing pages
- create duplicate components
- hardcode colors
- hardcode spacing
- hardcode typography
- introduce inconsistent layouts

---

# PAGE GENERATION RULES

Every generated page must:
- use existing layout wrapper
- use shared page header
- use shared card structure
- use shared table structure
- use shared form structure

Pages must visually match the existing application.

---

# FILE STRUCTURE RULES

Use:

resources/views/components/ui/
resources/views/components/forms/
resources/views/components/tables/
resources/views/components/modals/
resources/views/components/layout/
resources/views/components/feedback/

DO NOT:
- place reusable UI directly inside pages

---

# NAMING CONVENTIONS

Use:
- AppButton
- AppInput
- AppCard
- StatusBadge

DO NOT:
- BlueButton
- NewButton
- BigCard
- SpecialInput

Names must describe purpose, not appearance.

---

# DESIGN SYSTEM GOAL

The entire application must behave as a single cohesive product.

All future development must:
- feel visually unified
- feel structurally unified
- reuse existing patterns
- avoid fragmentation

Consistency has higher priority than novelty.
