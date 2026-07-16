# Theme Manager Production Implementation Plan

> Implement this plan against the existing Laravel application and existing `themes` and `theme_settings` tables. Do not create a migration.

## File Map

- Create `app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php` for the page query and active-theme update.
- Create `resources/views/awesome_admin/awesome_admin_themes.blade.php` for the CMS-integrated approved layout.
- Create `public/assets/js/vue3/manage_themes/vueV3-manage-themes-2026.js` for Vue/Axios interactions.
- Create `public/assets/images/themes/previews/*.png` as replaceable preview assets.
- Modify `routes/web.php` for authenticated Awesome Admin routes.
- Modify `resources/views/awesome_admin/awesome_admin.blade.php` for the Manage Themes menu item.
- Modify `public/mockups/theme-manager-interactive-mockup.html` to remove the redundant browse link.
- Modify `database/seeders_new/ThemesSeeder.php` only to keep Arunika V2 available on fresh installs; do not change schema or add migrations.
- Create focused PHP and Node tests.

## Task 1: Lock the production contract with failing tests

1. Add a feature test for the authenticated page, Awesome Admin menu link, existing-table persistence, allowed theme validation, and database-state restoration.
2. Add a static interaction contract test for the Blade, Vue script, mockup link removal, and preview assets.
3. Run both and confirm failure is caused by the missing implementation.

## Task 2: Back up and add the server-side feature

1. Back up every existing file that will be modified.
2. Add the new controller and authenticated routes.
3. Query only Arunika V1/V2 from `themes`, read the active code from `theme_settings`, and update the same row transactionally.
4. Add the Awesome Admin grid item and keep the existing Manage Appearance item unchanged.

## Task 3: Build the CMS-integrated interface

1. Port the approved mockup hierarchy and card visuals into a Blade view that extends the active CMS layout.
2. Use existing Arunika CSS variables and Bootstrap components with safe fallbacks.
3. Add responsive theme cards, Active/Selected states, Save/Cancel, preview modal, keyboard controls, toast, and loading/error feedback.
4. Copy the current previews into stable production asset paths.
5. Remove the browse link from the standalone mockup.

## Task 4: Verify and visually QA

1. Run focused PHP and Node tests, PHP/JavaScript syntax checks, route inspection, Blade compilation, and `git diff --check`.
2. Exercise the authenticated page in the in-app browser without leaving the database in a changed state.
3. Compare the production content area with the approved mockup at the same state and capture the result.
4. Append the production QA evidence to `design-qa.md` after backing it up.

