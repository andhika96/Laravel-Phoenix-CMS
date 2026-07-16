import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const source = readFileSync(
  path.join(process.cwd(), 'public/assets/js/themes/arunika_v3/arunika_v3.js'),
  'utf8',
);

test('collapsed menu popover lookup stops at the next navigation anchor', () => {
  assert.match(
    source,
    /nextEl\.matches\('a\.list-group-item'\)/,
    'Popover lookup must not cross into a later menu item.',
  );
  assert.doesNotMatch(
    source,
    /nextEl\.tagName\s*===\s*'a'/,
    'HTML tagName is uppercase, so the lowercase comparison never stops traversal.',
  );
});

test('a collapsed navigation item never opens tooltip and submenu popover together', () => {
  assert.match(
    source,
    /if\s*\(tooltip\s*&&\s*!\s*popover\)/,
    'Tooltip and parent-menu popover must be mutually exclusive.',
  );
});
