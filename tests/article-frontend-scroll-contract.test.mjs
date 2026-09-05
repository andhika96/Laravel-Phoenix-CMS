import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
	path.join(process.cwd(), 'public/assets/css/themes/arunika_lucent/arunika_lucent.css'),
	'utf8',
);

test('Lucent frontend layouts release the body scroll lock without changing admin shell scrolling', () => {
	const rule = css.match(/body\.ph-theme-arunika-lucent:not\(:has\(\.ph-app-shell\)\)\s*\{([^}]*)\}/s);

	assert.ok(rule, 'expected a scoped Lucent non-admin body scroll rule');
	assert.match(rule[1], /height\s*:\s*auto/);
	assert.match(rule[1], /min-height\s*:\s*100%/);
	assert.match(rule[1], /overflow-y\s*:\s*auto/);
});
