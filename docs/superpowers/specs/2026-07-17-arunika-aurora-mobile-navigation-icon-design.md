# Arunika Aurora Mobile Navigation Icon Design

## Goal

Make the Arunika Aurora mobile navigation control match the approved Arunika Canvas treatment: a visible panel SVG icon without a surrounding visual button shell.

## Approved direction

- Keep the semantic `<button>` and its existing `toggleSidebar()` interaction, label, and keyboard behavior.
- Reuse Aurora's existing desktop `.ph-sidebar-toggle-icon` SVG in the mobile trigger.
- Keep a 36px by 36px touch target, with an 18px by 18px SVG.
- Scope the visual reset to `.ph-mobile-sidebar-trigger`: no padding, border, background, radius, or shadow.
- Do not change the desktop sidebar toggle, drawer close control, Aurora layout, header spacing, or search behavior.

## Verification

- A static regression proves the mobile trigger contains the existing panel SVG and the Aurora CSS removes the button shell.
- Browser QA at 414px confirms the computed visual properties, no horizontal overflow, and sidebar open-close interaction.
- Desktop QA confirms the desktop toggle remains unchanged and the mobile trigger remains hidden.
