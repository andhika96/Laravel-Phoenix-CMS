import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const read = (relativePath) => readFileSync(path.join(root, relativePath), 'utf8');

test('Minimal Reading List keeps the option-driven header copy above its search toolbar', () => {
	const header = read('resources/views/article/templates/partials/archive-header.blade.php');
	const minimal = read('resources/views/article/templates/archive/minimal-reading-list.blade.php');
	const css = read('public/assets/css/article/article-frontend-2026.css');

	assert.match(header, /article-template-header--\{\{ \$templateKey \}\}/);
	assert.ok(header.indexOf('article-template-header__copy') < header.indexOf('article-template-toolbar'));
	assert.match(minimal, /article\.templates\.partials\.archive-header/);
	assert.match(css, /\.article-page--reading-list \.article-template-header--minimal-reading-list\s*\{[\s\S]*?display:\s*flex/);
	assert.match(css, /\.article-page--reading-list \.article-template-header--minimal-reading-list \.article-template-header__copy/);
});
