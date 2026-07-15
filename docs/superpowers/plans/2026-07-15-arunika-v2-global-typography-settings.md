# Arunika V2 Global Typography Settings Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the approved balanced General Settings layout production-ready, persist its typography controls, and apply the selected font family and base size across the Arunika v2 CMS immediately and after reload.

**Architecture:** Continue through the existing Awesome Admin Site Config form, Vue 3 + Axios submit flow, and `site_config` row. Render the saved typography as CSS variables and a selected local font stylesheet in the Arunika v2 CMS layout, then update the same variables and stylesheet in the successful Axios callback.

**Tech Stack:** Laravel 13, Blade, Eloquent, Vue 3 CDN, Axios, Bootstrap 5, Vue Select, local font assets.

## Global Constraints

- Keep the existing CMS technology and coding style.
- Use the approved balanced General Settings mockup as the visual source.
- Font units are exactly `px`, `em`, and `rem`; the default is `Nunito 14px`.
- Reuse the current `site_config` record and update endpoint; do not add another settings subsystem.
- Preserve all unrelated working-tree changes.

---

### Task 1: Lock the persistence and global-rendering contract

**Files:**
- Modify: `tests/Feature/SiteTypographyPreviewSettingsTest.php`

**Interfaces:**
- Consumes: `POST cms.admin.awesome_admin.config.update` with `font_family`, `font_size`, and `font_size_unit`.
- Produces: regression coverage for database persistence, allowed local fonts, balanced layout markers, server-rendered CSS variables, and live Vue application.

- [ ] Write assertions for the approved layout, saved local-font code, selected stylesheet, global CSS variables, and live apply method.
- [ ] Run `php artisan test tests/Feature/SiteTypographyPreviewSettingsTest.php` and verify the new assertions fail for missing production behavior.

### Task 2: Apply saved typography globally

**Files:**
- Modify: `app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Config_Controller.php`
- Modify: `resources/views/themes/arunika_v2/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_v2/arunika_v2.css`
- Modify: `public/assets/js/vue3/manage_config/vueV3-manage-config-2026.js`

**Interfaces:**
- Consumes: stored local font code such as `fira_sans`, numeric size, and unit.
- Produces: `#arunikaActiveFontStylesheet`, `--ph-font-family`, `--ph-font-size`, and `applySiteTypographySettings()`.

- [ ] Restrict `font_family` to installed local font directory codes.
- [ ] Render the selected local font stylesheet and safe CSS variables before the CMS body paints.
- [ ] Make the theme body consume the global family and size variables.
- [ ] Apply the selected family, stylesheet, and size immediately after successful Axios save.

### Task 3: Implement the approved balanced General Settings layout

**Files:**
- Modify: `resources/views/awesome_admin/awesome_admin_config.blade.php`
- Modify: `public/assets/js/vue3/manage_config/vueV3-manage-config-2026.js`

**Interfaces:**
- Consumes: existing Site Config fields, Vue Select, thumbnail upload, and typography preview state.
- Produces: `#siteInformationLayout`, `#siteThumbnailCard`, and full-width `#typographySettingsLayout` with responsive stacking.

- [ ] Rebuild only the General Settings section with the approved upper information/thumbnail band.
- [ ] Place font controls and preview in a full-width lower typography band.
- [ ] Keep thumbnail browse, drop, live preview, and reset functional without changing the backend field name `file`.
- [ ] Preserve Vue Select for Font Family.

### Task 4: Verify production behavior and visual fidelity

**Files:**
- Modify: `design-qa.md`

**Interfaces:**
- Consumes: approved mockup and authenticated production Site Config page.
- Produces: passing automated checks and a `final result: passed` QA entry.

- [ ] Run the focused feature and static tests, PHP/JS syntax checks, Blade compilation, and `git diff --check`.
- [ ] Save a non-default typography choice through the real page and verify the database, root CSS variables, loaded stylesheet, and another CMS page.
- [ ] Restore `Nunito 14px` after runtime verification.
- [ ] Capture desktop and mobile production screenshots, compare them with the approved mockup, fix P0/P1/P2 differences, and append the QA evidence.
