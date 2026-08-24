import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(process.cwd(), 'public/assets/js/vue3/article/vueV3-article-frontend-2026.js');
const source = readFileSync(sourcePath, 'utf8');

function loadOptions(assignments) {
	const sandbox = {
		Vue: {
			createApp(options) {
				return { mount() { return options; } };
			},
		},
		document: { getElementById() { return null; } },
		window: {
			location: {
				origin: 'https://laravel-13-phoenix.aruna',
				assign(url) { assignments.push(url); },
			},
		},
		URL,
		URLSearchParams,
	};

	vm.createContext(sandbox);
	vm.runInContext(`${source}\n;globalThis.__options = ArticleFrontendOptions;`, sandbox, { filename: sourcePath });

	return sandbox.__options;
}

test('Vue archive navigation preserves SSR query URLs instead of clearing the current result first', () => {
	const assignments = [];
	const options = loadOptions(assignments);
	const state = { isNavigating: false };
	const event = {
		preventDefault() {},
		currentTarget: { href: 'https://laravel-13-phoenix.aruna/article?search=design&page=2' },
	};

	options.methods.navigate.call(state, event);

	assert.equal(state.isNavigating, true);
	assert.deepEqual(assignments, ['https://laravel-13-phoenix.aruna/article?search=design&page=2']);
});

test('Vue archive filter submit preserves non-empty category and tag values in the SSR URL', () => {
	const assignments = [];
	const options = loadOptions(assignments);
	const state = { isNavigating: false };
	const event = {
		preventDefault() {},
		currentTarget: {
			action: 'https://laravel-13-phoenix.aruna/article',
			elements: [
				{ name: 'search', value: 'design systems' },
				{ name: 'category', value: '3' },
				{ name: 'tag', value: 'ux' },
			],
		},
	};

	options.methods.submitFilter.call(state, event);

	assert.equal(state.isNavigating, true);
	assert.deepEqual(assignments, ['https://laravel-13-phoenix.aruna/article?search=design+systems&category=3&tag=ux']);
});

test('Article archive pins a Vue 3 CDN production build and keeps its server-rendered fallback', () => {
	const archive = readFileSync(path.join(process.cwd(), 'resources/views/article/archive.blade.php'), 'utf8');

	assert.match(archive, /vue@3\.5\.21\/dist\/vue\.global\.prod\.js/);
	assert.doesNotMatch(archive, /vue@latest/);
	assert.match(archive, /@include\(\$archiveView/);
});

test('public pagination uses the CMS page-pill language and preserves SSR navigation hooks', () => {
	const pagination = readFileSync(path.join(process.cwd(), 'resources/views/article/templates/partials/pagination.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(pagination, /article-pagination__summary/);
	assert.match(pagination, /ph-pagination/);
	assert.match(pagination, /t\('First page'\)/);
	assert.match(pagination, /t\('Last page'\)/);
	assert.match(pagination, /data-article-pagination-link/);
	assert.match(css, /article-pagination__summary/);
	assert.match(css, /article-pagination \.ph-pagination/);
});
