import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const root = process.cwd();
const read = (relativePath) => readFileSync(path.join(root, relativePath), 'utf8');

function loadArticleOptions() {
	const sandbox = {
		Vue: { createApp(options) { return { mount() { return options; } }; } },
		document: { getElementById() { return null; } },
		window: {
			location: { origin: 'https://laravel-13-phoenix.aruna' },
			history: { pushState() {}, replaceState() {} },
			addEventListener() {},
			removeEventListener() {},
		},
		URL,
		URLSearchParams,
		fetch: async () => ({ ok: true, json: async () => ({ success: true }) }),
	};

	vm.createContext(sandbox);
	vm.runInContext(`${read('public/assets/js/vue3/article/vueV3-article-frontend-2026.js')}\n;globalThis.__options = ArticleFrontendOptions;`, sandbox);

	return sandbox.__options;
}

test('Minimal category filter has one option-driven mode and no duplicate controls', () => {
	const options = read('app/Support/Article/ArticleTemplateOptions.php');
	const header = read('resources/views/article/templates/partials/archive-header.blade.php');
	const minimal = read('resources/views/article/templates/archive/minimal-reading-list.blade.php');

	assert.match(options, /\$result\['toolbar'\]\['category'\]\['mode'\]\s*=\s*\$this->categoryMode\(\$input\)/);
	assert.match(options, /private function categoryMode\(array \$input\)/);
	assert.match(header, /\$showCategorySelect/);
	assert.match(header, /data-article-category-select/);
	assert.match(header, /@if \(\$showCategorySelect\s*&&/);
	assert.match(header, /!\$isMinimalReadingList \|\| \$categoryMode === 'select'/);
	assert.match(minimal, /take\(10\)/);
	assert.match(minimal, /data-article-category-search/);
	assert.match(minimal, /data-article-category-link/);
});

test('Vue 3 category interactions stay asynchronous and client-filter the visible category buttons', () => {
	const source = read('public/assets/js/vue3/article/vueV3-article-frontend-2026.js');

	assert.match(source, /categorySearch:\s*''/);
	assert.match(source, /navigateCategory\(event\)/);
	assert.match(source, /filterCategoryOptions\(\)/);
	assert.match(source, /data-article-category-search/);
	assert.match(source, /data-article-category-link/);
	assert.match(source, /data-article-category-select/);
	assert.match(source, /this\.loadArchive\(new URL\(href, window\.location\.origin\), 'data'\)/);
});

test('Vue category click uses the existing async archive loader and select changes submit automatically', () => {
	const options = loadArticleOptions();
	const link = { href: 'https://laravel-13-phoenix.aruna/article?category=7' };
	const asyncState = {
		isNavigating: false,
		loadArchive(url, mode) { this.loaded = { url: url.toString(), mode }; },
	};
	let prevented = false;

	options.methods.navigateCategory.call(asyncState, {
		preventDefault() { prevented = true; },
		target: { closest() { return link; } },
	});

	assert.equal(prevented, true);
	assert.equal(asyncState.isNavigating, true);
	assert.deepEqual(asyncState.loaded, {
		url: 'https://laravel-13-phoenix.aruna/article?category=7',
		mode: 'data',
	});

	const form = { action: 'https://laravel-13-phoenix.aruna/article', closest() { return this; } };
	const selectState = {
		filters: { search: 'focus', category: '', tag: '' },
		submitFilter(event) { this.submitted = event.target; },
	};
	const select = {
		name: 'category',
		value: '9',
		matches(selector) { return selector === '[data-article-category-select]'; },
		closest() { return form; },
	};

	options.methods.syncFilterInput.call(selectState, { target: select });

	assert.equal(selectState.filters.category, '9');
	assert.equal(selectState.submitted, form);
});

test('Minimal post-list spacing and sidebar positions are normalized and exposed to the manager', () => {
	const options = read('app/Support/Article/ArticleTemplateOptions.php');
	const minimal = read('resources/views/article/templates/archive/minimal-reading-list.blade.php');
	const manager = read('resources/views/manage_article/templates/index.blade.php');
	const managerJs = read('public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js');
	const managerCss = read('public/assets/css/article/article-template-manager-2026.css');

	assert.match(options, /sidebar\.categories\.position/);
	assert.match(options, /sidebar\.popular\.position/);
	assert.match(options, /post_list\.item_gap/);
	assert.doesNotMatch(options, /sidebar\.popular\.item_gap/);
	assert.match(minimal, /data-article-sidebar-position/);
	assert.match(minimal, /article-reading-list-post-gap/);
	assert.doesNotMatch(minimal, /article-reading-list-popular-gap/);
	assert.match(manager, /Category filter style/);
	assert.match(manager, /Form select/);
	assert.match(manager, /Button list/);
	assert.match(manager, /Categories position/);
	assert.match(manager, /Popular Posts position/);
	assert.match(manager, /Post list spacing/);
	assert.doesNotMatch(manager, /Popular post spacing/);
	assert.match(managerJs, /sidebar\.categories\.position/);
	assert.match(managerJs, /sidebar\.popular\.position/);
	assert.match(managerJs, /post_list\.item_gap/);
	assert.doesNotMatch(managerJs, /sidebar\.popular\.item_gap/);
	assert.match(managerCss, /\.article-template-toolbar-option-controls/);
	assert.match(managerCss, /\.article-template-reading-list-control/);
});

test('Minimal Reading List starts directly with article rows after its toolbar', () => {
	const minimal = read('resources/views/article/templates/archive/minimal-reading-list.blade.php');

	assert.doesNotMatch(minimal, /article-reading-list__section-heading/);
	assert.doesNotMatch(minimal, /Latest Articles/);
});
