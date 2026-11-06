# Phoenix authentication themes — approved review, 6 November 2026

Implement the reviewed login, signup, forgot-password and reset-password theme designs and Manage Appearance layout. User authorized implementation after reviewing interactive previews in this chat.

- Retain Default, Card, Split Left and Split Right. Add Clean Peach, Ocean Split, Midnight Story and Verdant Split for all four pages.
- Keep Blade auth layout, Vue 3, Bootstrap, Font Awesome, Coloris, existing fonts, account requests, validation and routes. Do not add another framework or social-login integration.
- Group auth settings by page tabs, theme gallery, colors/branding, page content, theme-specific visual options and responsive options. Keep other CMS settings intact.
- All reviewed controls persist and affect the rendered auth page. Options are scoped to page and theme and retained on theme switches.
- Selecting an image starts real upload immediately. Show transfer/processing progress; final 100% and Preview/Remove appear only after successful server storage. Removing an image clears its stored reference and leaves a neutral gray image area. Handle replacement failures without losing the previous image.
- Image upload covers background, panel image, logo, illustration and multiple avatars. Validate types/size, authenticate/authorize every mutation, restrict paths and slots, and do not delete shared or historical assets.
- Keep thumbnails, forms and dialogs usable on desktop, tablet and mobile. Preserve keyboard focus, labels, error feedback and responsive settings.
- Add only scoped schema changes; back up source and auth database rows before changes. Do not change selected theme or overwrite the user's settings simply to install the themes.
- All backups, plans, tests and QA artifacts belong here. Graphify stays unchanged until the user explicitly requests an update. No staging, commit, push or deployment.

References and exact option lists: reviewed previews in project-artifacts/auth-appearance-layout-preview-20261106/auth-appearance-layout.html and project-artifacts/auth-theme-preview-20261106/phoenix-auth-preview.html.
