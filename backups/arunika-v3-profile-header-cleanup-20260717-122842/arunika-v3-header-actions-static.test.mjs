import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const layout = readFileSync(
  path.join(root, 'resources/views/themes/arunika_v3/cms/cms_layout.blade.php'),
  'utf8',
);
const css = readFileSync(
  path.join(root, 'public/assets/css/themes/arunika_v3/arunika_v3.css'),
  'utf8',
);

test('Arunika V3 keeps notifications mounted but hidden before the admin shortcut', () => {
  const headerActionsIndex = layout.indexOf('class="ph-header-actions"');
  const hiddenNotificationIndex = layout.indexOf('class="ph-header-notification is-hidden"', headerActionsIndex);
  const notificationIncludeIndex = layout.indexOf("@include('components.cms-realtime-notification')", headerActionsIndex);
  const adminShortcutIndex = layout.indexOf('class="ph-btn-action-icon ph-header-awesome-admin"', headerActionsIndex);

  assert.ok(headerActionsIndex >= 0, 'Missing V3 header actions container.');
  assert.ok(hiddenNotificationIndex > headerActionsIndex, 'Notification must use the V3 hidden wrapper.');
  assert.ok(notificationIncludeIndex > hiddenNotificationIndex, 'Notification component must remain mounted.');
  assert.ok(adminShortcutIndex > notificationIncludeIndex, 'Awesome Admin must be the final header action.');
});

test('Arunika V3 renders an admin-only user-secret shortcut to Awesome Admin', () => {
  assert.match(
    layout,
    /class="ph-header-actions"[\s\S]*?@if\(checkIsAdmin\(\)\)[\s\S]*?<a href="\{\{ url\('awesome_admin'\) \}\}" class="ph-btn-action-icon ph-header-awesome-admin"[^>]*>[\s\S]*?<i class="fal fa-user-secret"><\/i>[\s\S]*?<\/a>[\s\S]*?@endif/,
  );
});

test('Arunika V3 header actions use proportional button and glyph sizes', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-btn-action-icon\s*\{[^}]*width:\s*34px;[^}]*height:\s*34px;[^}]*font-size:\s*16px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-header-awesome-admin\s+i,\s*\.ph-theme-arunika-v3\s+\.ph-header-notification\s+#cmsNotifBell\s+i\s*\{[^}]*font-size:\s*16px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-header-notification\.is-hidden\s*\{[^}]*display:\s*none\s*!important;/s,
  );
});

test('Arunika V3 profile dropdown preserves dynamic account actions in the reference card order', () => {
  const profileTriggerIndex = layout.indexOf('class="ph-sidebar-user-card"');
  const menuIndex = layout.indexOf('class="dropdown-menu ph-header-profile-menu ph-sidebar-profile-menu"', profileTriggerIndex);
  const userSummaryIndex = layout.indexOf('class="ph-profile-menu-user"', menuIndex);
  const profileIndex = layout.indexOf("href=\"{{ url('profile') }}\"", menuIndex);
  const settingsGuardIndex = layout.indexOf('@if(checkIsAdmin())', profileIndex);
  const settingsIndex = layout.indexOf("href=\"{{ url('awesome_admin') }}\"", settingsGuardIndex);
  const darkModeIndex = layout.indexOf('class="dropdown-item ph-profile-theme-toggle ph-theme-toggle"', settingsIndex);
  const colorToggleIndex = layout.indexOf('class="dropdown-item ph-profile-color-toggle collapsed"', darkModeIndex);
  const colorContainerIndex = layout.indexOf('id="color-picker-container"', colorToggleIndex);
  const logoutIndex = layout.indexOf("href=\"{{ url('auth/logout') }}\"", colorContainerIndex);

  assert.ok(profileTriggerIndex >= 0, 'Missing sidebar profile trigger.');
  assert.match(
    layout.slice(profileTriggerIndex, menuIndex),
    /data-bs-toggle="dropdown"[^>]*data-bs-display="static"[^>]*data-bs-auto-close="outside"/,
  );
  assert.ok(userSummaryIndex > menuIndex, 'Dynamic user summary must begin the dropdown.');
  assert.match(
    layout.slice(userSummaryIndex, profileIndex),
    /get_avatar\('frame', 'rounded-circle', 36\)/,
  );
  assert.match(layout.slice(userSummaryIndex, profileIndex), /auth\(\)->user\(\)->fullname/);
  assert.match(layout.slice(userSummaryIndex, profileIndex), /\{\{ \$currentUserRole \}\}/);
  assert.ok(profileIndex > userSummaryIndex, 'Profile action must follow the user summary.');
  assert.ok(settingsGuardIndex > profileIndex, 'Settings must remain after Profile.');
  assert.ok(settingsIndex > settingsGuardIndex, 'Settings must remain guarded for administrators.');
  assert.ok(darkModeIndex > settingsIndex, 'Dark Mode must follow account actions.');
  assert.ok(colorToggleIndex > darkModeIndex, 'Theme Color must follow Dark Mode.');
  assert.ok(colorContainerIndex > colorToggleIndex, 'Theme swatches must remain inside Theme Color.');
  assert.ok(logoutIndex > colorContainerIndex, 'Logout must remain the final profile action.');
});

test('Arunika V3 profile dropdown wires the Dark Mode switch and collapsible theme colors', () => {
  assert.match(
    layout,
    /class="dropdown-item ph-profile-theme-toggle ph-theme-toggle"[^>]*onclick="toggleTheme\(\)"[^>]*aria-pressed="false"[\s\S]*?class="ph-profile-switch"/,
  );
  assert.match(
    layout,
    /class="dropdown-item ph-profile-color-toggle collapsed"[^>]*data-bs-toggle="collapse"[^>]*data-bs-target="#ph-profile-theme-colors"[^>]*aria-expanded="false"[^>]*aria-controls="ph-profile-theme-colors"/,
  );
  assert.match(
    layout,
    /class="collapse ph-profile-color-collapse" id="ph-profile-theme-colors"[\s\S]*?id="color-picker-container"/,
  );
});

test('Arunika V3 profile dropdown uses the compact reference card styling', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-header-profile-menu\s*\{[^}]*width:\s*240px;[^}]*min-width:\s*240px;[^}]*padding:\s*6px;[^}]*border:\s*1px solid var\(--ph-v3-border\);[^}]*border-radius:\s*14px;[^}]*box-shadow:/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-profile-menu-user\s*\{[^}]*min-height:\s*58px;[^}]*padding:\s*8px;[^}]*border-radius:\s*10px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-header-profile-menu\s+\.dropdown-item\s*\{[^}]*min-height:\s*40px;[^}]*padding:\s*0 10px;[^}]*border-radius:\s*8px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-profile-switch\s*\{[^}]*width:\s*32px;[^}]*height:\s*18px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-profile-theme-toggle\.is-dark\s+\.ph-profile-switch\s*\{[^}]*background:\s*var\(--ph-theme-primary\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-profile-color-toggle:not\(\.collapsed\)\s+\.ph-profile-color-chevron\s*\{[^}]*transform:\s*rotate\(90deg\);/s,
  );
});
