import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();

test('curated template preview identifies sample content and prevents fixture links from leaving the iframe', () => {
	const preview = readFileSync(path.join(root, 'resources/views/manage_article/templates/preview.blade.php'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(preview, /data-preview-fixture="true"/);
	assert.match(preview, /event\.preventDefault\(\)/);
	assert.match(preview, /Sample editorial content/);
	assert.match(css, /body\[data-preview-fixture="true"\] a/);
	assert.match(preview, /article-theme-color-sync-2026\.js/);
});

test('public detail wrapper passes neighboring Articles through to every selected detail template', () => {
	const detail = readFileSync(path.join(root, 'resources/views/article/detail.blade.php'), 'utf8');

	assert.match(detail, /previousArticle/);
	assert.match(detail, /nextArticle/);
});
