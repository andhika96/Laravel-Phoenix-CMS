import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
	path.join(process.cwd(), 'public/assets/css/article/article-frontend-2026.css'),
	'utf8',
);

test('Article pagination active page keeps readable text over a themed background', () => {
	const rule = css.match(/\.article-pagination \.ph-pagination \.active > \.page-link\s*\{([^}]*)\}/s);

	assert.ok(rule, 'expected an Article-scoped active pagination rule');
	assert.match(rule[1], /color\s*:\s*#fff\s*!important/);
});
