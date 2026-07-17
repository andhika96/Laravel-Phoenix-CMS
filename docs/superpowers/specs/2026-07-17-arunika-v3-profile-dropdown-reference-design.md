# Arunika V3 Profile Dropdown Reference Design

## Goal

Restyle the Arunika V3 sidebar profile dropdown to follow the supplied compact white-card reference while preserving the CMS's existing dynamic data and behavior.

## Visual contract

- Keep the dropdown anchored to the right of the sidebar profile card on desktop and above the profile card on mobile.
- Use a 240px-wide popover with a 14px radius, subtle theme border, panel background, and soft elevated shadow.
- Show a compact user summary at the top with the existing dynamic avatar, full name, and current role; do not render a trailing chevron.
- Use consistent 40px menu rows with existing Font Awesome icons, restrained hover states, and 3px vertical item margins so adjacent hover surfaces remain visually separated. Reset the group edges with `:first-child` and `:last-child`.
- Keep the transition from the user summary into the primary actions borderless, while retaining a lightweight divider before Logout.
- Match the reference's compact spacing and visual hierarchy without copying its sample user data or unavailable PRO badge.

## Behavior contract

- Preserve the existing dynamic Profile link.
- Preserve the existing admin-only Settings link and `checkIsAdmin()` guard.
- Preserve Dark Mode through the existing `toggleTheme()` function and `.ph-theme-toggle` state synchronization.
- Display Dark Mode with a proportional switch indicator.
- Keep all seven existing theme colors and the current `#color-picker-container`; reveal them through a collapsible Theme Color row.
- Keep the dropdown open while interacting with Dark Mode or Theme Color by using Bootstrap's `data-bs-auto-close="outside"`.
- Preserve the existing Logout route.
- Do not add routes, JavaScript dependencies, image assets, or hard-coded user data.

## Responsive and theme requirements

- Preserve the existing mobile dropdown positioning and make its width fluid in the sidebar.
- Use existing Arunika V3 tokens for surface, text, border, muted text, and primary color.
- The profile card, controls, switch, and color palette must remain legible in light and dark modes.

## Verification

- Static tests must lock the Blade structure, dynamic content, admin guard, action order, collapse wiring, popover dimensions, switch styling, and chevron state.
- Existing Laravel tests must remain green.
- Browser QA should compare the supplied reference and the rendered authenticated dropdown at the same open state. If authenticated capture is unavailable, record the visual QA as blocked rather than claiming a visual pass.
