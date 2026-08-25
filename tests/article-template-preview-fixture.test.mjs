import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const root = process.cwd();

test('curated template preview identifies sample content and prevents fixture links from leaving the iframe', () => {
	const preview = readFileSync(path.join(root, 'resources/views/manage_article/templates/preview.blade.php'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(preview, /data-preview-fixture="true"/);
	assert.match(preview, /event\.preventDefault\(\)/);
	assert.match(preview, /Sample editorial content/);
	assert.match(css, /body\[data-preview-fixture="true"\] a/);
	assert.match(preview, /article-theme-color-sync-2026\.js/);
	assert.match(preview, /fontawesome\/5\.15\.3\/css\/all\.min\.css/);
	assert.doesNotMatch(preview, /article-page \.article-shell[^}]*padding-top:2rem/);
	assert.doesNotMatch(preview, /article-detail__shell[^}]*padding-top:2rem/);
});

test('curated template preview blocks archive toolbar submissions from leaving the draft iframe', () => {
	const preview = readFileSync(path.join(root, 'resources/views/manage_article/templates/preview.blade.php'), 'utf8');
	const fixtureGuard = [...preview.matchAll(/<script>([\s\S]*?)<\/script>/g)].at(-1)?.[1];
	const listeners = new Map();

	assert.ok(fixtureGuard, 'expected the fixture-only interaction guard');
	vm.runInNewContext(fixtureGuard, {
		document: {
			addEventListener(type, listener) {
				listeners.set(type, listener);
			},
		},
	});

	assert.equal(typeof listeners.get('submit'), 'function');

	let prevented = false;
	listeners.get('submit')({
		target: {
			closest(selector) {
				return selector === '[data-article-filter]' ? {} : null;
			},
		},
		preventDefault() {
			prevented = true;
		},
	});

	assert.equal(prevented, true);
});

test('public detail wrapper passes neighboring Articles through to every selected detail template', () => {
	const detail = readFileSync(path.join(root, 'resources/views/article/detail.blade.php'), 'utf8');

	assert.match(detail, /previousArticle/);
	assert.match(detail, /nextArticle/);
});
