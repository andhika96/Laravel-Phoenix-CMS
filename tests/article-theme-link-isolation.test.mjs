import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
	path.join(process.cwd(), 'public/assets/css/article/article-frontend-2026.css'),
	'utf8',
);

function ruleFor(selector) {
	return [...css.matchAll(new RegExp(`${selector}\\s*\\{([^}]*)\\}`, 'gs'))].at(-1)?.[1] || '';
}

test('Article link hierarchy stays readable when a CMS theme tints generic anchors', () => {
	assert.match(css, /\.article-page \.article-title-clamp a,[\s\S]*?\.article-detail \.article-toc a\s*\{[^}]*color\s*:\s*inherit\s*!important/);
	assert.match(ruleFor('\\.article-pagination \\.ph-pagination \\.page-link'), /color\s*:\s*#344054\s*!important/);
	assert.match(ruleFor('body \\.article-detail \\.article-detail-navigation__item'), /color\s*:\s*#344054\s*!important/);
});
