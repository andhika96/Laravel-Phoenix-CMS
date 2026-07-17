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
