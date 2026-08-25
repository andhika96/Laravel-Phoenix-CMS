import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(process.cwd(), 'public/assets/js/vue3/article/vueV3-article-frontend-2026.js');
const source = readFileSync(sourcePath, 'utf8');

function loadOptions({ assignments = [], fetchImpl = null, fetchResponse = null } = {}) {
	const historyActions = [];
	const listeners = {};
	const sandbox = {
		Vue: {
			createApp(options) {
				return { mount() { return options; } };
			},
		},
		document: {
			getElementById() { return null; },
			querySelector() { return null; },
			querySelectorAll() { return []; },
		},
		window: {
			location: {
				origin: 'https://laravel-13-phoenix.aruna',
				href: 'https://laravel-13-phoenix.aruna/article',
				assign(url) { assignments.push(url); },
			},
			history: {
				pushState(_state, _title, url) { historyActions.push({ type: 'push', url: String(url) }); },
				replaceState(_state, _title, url) { historyActions.push({ type: 'replace', url: String(url) }); },
			},
			addEventListener(type, listener) { listeners[type] = listener; },
			removeEventListener(type) { delete listeners[type]; },
		},
		fetch: fetchImpl || (async () => fetchResponse),
		URL,
		URLSearchParams,
	};

	vm.createContext(sandbox);
	vm.runInContext(`${source}\n;globalThis.__options = ArticleFrontendOptions;globalThis.__passwordOptions = ArticlePasswordGateOptions;`, sandbox, { filename: sourcePath });

	return { article: sandbox.__options, password: sandbox.__passwordOptions, historyActions, listeners };
}

test('Vue archive navigation preserves SSR query URLs instead of clearing the current result first', () => {
	const assignments = [];
	const options = loadOptions({ assignments }).article;
	const state = { isNavigating: false, loadArchive(url) { this.loadedUrl = url.toString(); } };
	const event = {
		preventDefault() {},
		target: { closest() { return { href: 'https://laravel-13-phoenix.aruna/article?search=design&page=2' }; } },
	};

	options.methods.navigate.call(state, event);

	assert.equal(state.isNavigating, true);
	assert.equal(state.loadedUrl, 'https://laravel-13-phoenix.aruna/article?search=design&page=2');
	assert.deepEqual(assignments, []);
});

test('Vue archive filter submit preserves its non-empty reactive values in the SSR URL', () => {
	const assignments = [];
	const options = loadOptions({ assignments }).article;
	const state = {
		filters: { search: 'design systems', category: '3', tag: 'ux' },
		isNavigating: false,
		loadArchive(url) { this.loadedUrl = url.toString(); },
	};
	const form = {
		action: 'https://laravel-13-phoenix.aruna/article',
		elements: [
			{ name: 'search', value: 'design systems' },
			{ name: 'category', value: '3' },
			{ name: 'tag', value: 'ux' },
		],
	};
	const event = {
		preventDefault() {},
		target: { closest() { return form; } },
	};

	options.methods.submitFilter.call(state, event);

	assert.equal(state.isNavigating, true);
	assert.equal(state.loadedUrl, 'https://laravel-13-phoenix.aruna/article?search=design+systems&category=3&tag=ux');
	assert.deepEqual(assignments, []);
});

test('Vue owns delegated archive search and category values before a filter request', () => {
	const options = loadOptions().article;
	const state = {
		filters: { search: '', category: '', tag: '' },
		isNavigating: false,
		loadArchive(url) { this.loadedUrl = url.toString(); },
	};
	const filterForm = {
		action: 'https://laravel-13-phoenix.aruna/article',
		closest() { return this; },
	};

	options.methods.syncFilterInput.call(state, {
		target: { name: 'search', value: 'design systems', closest() { return filterForm; } },
	});
	options.methods.syncFilterInput.call(state, {
		target: { name: 'category', value: '3', closest() { return filterForm; } },
	});

	assert.deepEqual(state.filters, { search: 'design systems', category: '3', tag: '' });

	options.methods.submitFilter.call(state, {
		preventDefault() {},
		target: { closest() { return filterForm; } },
	});

	assert.equal(state.loadedUrl, 'https://laravel-13-phoenix.aruna/article?search=design+systems&category=3');
});

test('Vue replaces only the initial archive history entry and reloads browser history without pushing', async () => {
	const { article: options, historyActions } = loadOptions();
	const state = {
		error: '',
		filters: { search: '', category: '', tag: '' },
		isNavigating: false,
		loadingData: false,
		loadingNextPage: false,
		requestSequence: 0,
		syncFiltersFromUrl() {},
		async fetchList() { return true; },
	};

	await options.methods.loadArchive.call(state, new URL('https://laravel-13-phoenix.aruna/article'), 'data', 'replace');
	assert.deepEqual(historyActions, [{ type: 'replace', url: 'https://laravel-13-phoenix.aruna/article' }]);

	const popstateState = {
		loadArchive(url, mode, historyMode) { this.loaded = [url.toString(), mode, historyMode]; },
	};
	options.methods.handlePopstate.call(popstateState);
	assert.deepEqual(popstateState.loaded, ['https://laravel-13-phoenix.aruna/article', 'data', 'none']);
});

test('Vue ignores an older page response after a newer page request wins', async () => {
	const pendingResponses = [];
	const { article: options, historyActions } = loadOptions({
		fetchImpl: () => new Promise((resolve) => pendingResponses.push(resolve)),
	});
	const state = {
		renderedHtml: '',
		currentPage: 1,
		error: '',
		filters: { search: '', category: '', tag: '' },
		isHydrated: false,
		isNavigating: false,
		limit: 12,
		listUrl: 'https://laravel-13-phoenix.aruna/article/listdata',
		loadingData: false,
		loadingNextPage: false,
		requestSequence: 0,
		total: 0,
		totalPage: 1,
		replaceListHtml(html) { this.renderedHtml = html; },
		syncFiltersFromUrl() {},
		fetchList: options.methods.fetchList,
	};

	const pageTwo = options.methods.loadArchive.call(state, new URL('https://laravel-13-phoenix.aruna/article?page=2'), 'page');
	const pageThree = options.methods.loadArchive.call(state, new URL('https://laravel-13-phoenix.aruna/article?page=3'), 'page');

	pendingResponses[1]({ ok: true, json: async () => ({ success: true, html: '<section>Page three</section>', total: 36, total_page: 3, current_page: 3, limit: 12 }) });
	await pageThree;
	pendingResponses[0]({ ok: true, json: async () => ({ success: true, html: '<section>Page two</section>', total: 36, total_page: 3, current_page: 2, limit: 12 }) });
	await pageTwo;

	assert.equal(state.renderedHtml, '<section>Page three</section>');
	assert.equal(state.currentPage, 3);
	assert.deepEqual(historyActions, [{ type: 'push', url: 'https://laravel-13-phoenix.aruna/article?page=3' }]);
});

test('Vue archive data list requests the public endpoint and updates renderer HTML without navigation', async () => {
	const requests = [];
	const options = loadOptions({
		fetchResponse: {
			ok: true,
			json: async () => ({ success: true, html: '<section>Rendered list</section>', total: 12, total_page: 3, current_page: 2 }),
		},
	}).article;
	const state = {
		listUrl: 'https://laravel-13-phoenix.aruna/article/listdata',
		renderedHtml: '',
		loading: false,
		requestUrl: '',
		replaceListHtml(html) { this.renderedHtml = html; },
		updateUrl(url) { this.requestUrl = url.toString(); },
		async fetchList(url) { requests.push(url.toString()); return options.methods.fetchList.call(this, url); },
	};

	await options.methods.fetchList.call(state, new URL('https://laravel-13-phoenix.aruna/article?search=design&page=2'));

	assert.deepEqual(requests, []);
	assert.equal(state.renderedHtml, '<section>Rendered list</section>');
	assert.equal(state.total, 12);
	assert.equal(state.totalPage, 3);
	assert.equal(state.currentPage, 2);
});

test('Vue pagination component changes only the archive page query while preserving active filters', () => {
	const options = loadOptions().article;
	const state = {
		loadArchive(url) { this.loadedUrl = url.toString(); },
	};

	options.methods.goToPage.call(state, 3, 'https://laravel-13-phoenix.aruna/article?search=design&category=2');

	assert.equal(state.loadedUrl, 'https://laravel-13-phoenix.aruna/article?search=design&category=2&page=3');
});

test('Vue exposes distinct initial/filter and next/previous loading states while data is pending', async () => {
	let resolveResponse;
	const options = loadOptions({
		fetchImpl: () => new Promise((resolve) => { resolveResponse = resolve; }),
	}).article;
	const state = {
		renderedHtml: '',
		currentPage: 1,
		error: '',
		isHydrated: false,
		isNavigating: false,
		limit: 12,
		listUrl: 'https://laravel-13-phoenix.aruna/article/listdata',
		loadingData: false,
		loadingNextPage: false,
		total: 0,
		totalPage: 1,
		replaceListHtml() {},
		fetchList: options.methods.fetchList,
	};

	const pendingPage = options.methods.loadArchive.call(state, new URL('https://laravel-13-phoenix.aruna/article?page=2'), 'page');
	assert.equal(state.loadingData, false);
	assert.equal(state.loadingNextPage, true);
	assert.equal(options.computed.isLoading.call(state), true);
	resolveResponse({ ok: true, json: async () => ({ success: true, html: '<section>Page two</section>', total: 24, total_page: 2, current_page: 2, limit: 12 }) });
	await pendingPage;
	assert.equal(state.loadingNextPage, false);
	assert.equal(options.computed.isLoading.call(state), false);

	const pendingData = options.methods.loadArchive.call(state, new URL('https://laravel-13-phoenix.aruna/article?search=design'), 'data');
	assert.equal(state.loadingData, true);
	assert.equal(state.loadingNextPage, false);
	assert.equal(options.computed.isLoading.call(state), true);
	resolveResponse({ ok: true, json: async () => ({ success: true, html: '<section>Search result</section>', total: 1, total_page: 1, current_page: 1, limit: 12 }) });
	await pendingData;
	assert.equal(state.loadingData, false);
});

test('Vue password modal exposes the incorrect-password state without navigating away', async () => {
	const assignments = [];
	const options = loadOptions({
		assignments,
		fetchResponse: {
			ok: false,
			json: async () => ({ success: false, message: 'The password is incorrect. Please try again.' }),
		},
	}).password;
	const state = {
		error: '',
		isSubmitting: false,
		password: 'wrong-password',
		unlockUrl: 'https://laravel-13-phoenix.aruna/article/protected/unlock',
	};

	await options.methods.unlock.call(state, { preventDefault() {} });

	assert.equal(state.error, 'The password is incorrect. Please try again.');
	assert.equal(state.isSubmitting, false);
	assert.deepEqual(assignments, []);
});

test('Article archive pins a Vue 3 CDN production build and keeps its server-rendered fallback', () => {
	const archive = readFileSync(path.join(process.cwd(), 'resources/views/article/archive.blade.php'), 'utf8');

	assert.match(archive, /vue@3\.5\.21\/dist\/vue\.global\.prod\.js/);
	assert.doesNotMatch(archive, /vue@latest/);
	assert.match(archive, /@include\(\$archiveView/);
});

test('Article frontend compiles search, category filter, and pagination through Vue while retaining SSR markup', () => {
	const archive = readFileSync(path.join(process.cwd(), 'resources/views/article/archive.blade.php'), 'utf8');
	const header = readFileSync(path.join(process.cwd(), 'resources/views/article/templates/partials/archive-header.blade.php'), 'utf8');
	const pagination = readFileSync(path.join(process.cwd(), 'resources/views/article/templates/partials/pagination.blade.php'), 'utf8');
	const passwordViewPath = path.join(process.cwd(), 'resources/views/article/password-protected.blade.php');

	assert.match(source, /\.mount\('#ph-app-article-frontend'\)/);
	assert.doesNotMatch(archive, /v-html="archiveHtml"/);
	assert.match(archive, /data-article-ssr v-once/);
	assert.match(archive, /<paginate/);
	assert.match(archive, /vuejs-paginate-next/);
	assert.match(archive, /<div class="article-frontend-app"[^>]*v-on:submit\.prevent="handleSubmit"/);
	assert.match(archive, /<div class="article-frontend-app"[^>]*v-on:click="handleClick"/);
	assert.match(header, /data-article-filter/);
	assert.match(pagination, /data-article-pagination-link/);
	assert.match(source, /fetchList/);
	assert.match(source, /goToPage/);
	assert.match(source, /VuejsPaginateNext/);
	assert.match(source, /handleSubmit/);
	assert.match(source, /handleClick/);
	assert.match(source, /syncFilterInput/);
	assert.match(source, /loadingData/);
	assert.match(source, /loadingNextPage/);
	assert.match(archive, /v-on:input="syncFilterInput"/);
	assert.match(archive, /v-on:change="syncFilterInput"/);
	assert.match(archive, /is-loading-list/);
	assert.match(archive, /<Teleport/);
	assert.match(archive, /to="\[data-article-vue-control-slot\]"/);
	assert.match(archive, /to="\[data-article-vue-list-state-slot\]"/);
	assert.match(archive, /article-pagination--vue" :class="paginationClasses" :style="paginationStyle"/);
	assert.match(archive, /data-pagination-prev="<i class='fas fa-chevron-left'/);
	assert.match(archive, /data-pagination-next="<i class='fas fa-chevron-right'/);
	assert.doesNotMatch(archive, /far fa-chevron/);
	assert.match(source, /window\.history\.pushState/);
	assert.equal(existsSync(passwordViewPath), true);
	const passwordView = readFileSync(passwordViewPath, 'utf8');
	assert.match(passwordView, /v-on:submit\.prevent="unlock"/);
	assert.match(passwordView, /v-bind:type="showPassword \? 'text' : 'password'"/);
	assert.doesNotMatch(passwordView, /type="password" v-bind:type=/);
	assert.match(passwordView, /article-password-modal__close/);
});

test('public pagination uses the CMS page-pill language and preserves SSR navigation hooks', () => {
	const pagination = readFileSync(path.join(process.cwd(), 'resources/views/article/templates/partials/pagination.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(pagination, /article-pagination__summary/);
	assert.match(pagination, /ph-pagination/);
	assert.match(pagination, /t\('First page'\)/);
	assert.match(pagination, /t\('Last page'\)/);
	assert.match(pagination, /data-article-pagination-link/);
	assert.match(pagination, /fas fa-chevron-left/);
	assert.match(pagination, /fas fa-chevron-right/);
	assert.doesNotMatch(pagination, /far fa-chevron/);
	assert.match(css, /article-pagination__summary/);
	assert.match(css, /article-pagination \.ph-pagination/);
});

test('Vue archive controls and Manage Article-style loaders use stable renderer slots', () => {
	const archive = readFileSync(path.join(process.cwd(), 'resources/views/article/archive.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-frontend-2026.css'), 'utf8');
	const pagination = readFileSync(path.join(process.cwd(), 'resources/views/article/templates/partials/pagination.blade.php'), 'utf8');
	const archiveTemplates = [
		'balanced-card-grid.blade.php',
		'editorial-journal.blade.php',
		'minimal-reading-list.blade.php',
		'mosaic-classic.blade.php',
		'mosaic-magazine.blade.php',
	];

	for (const template of archiveTemplates) {
		const templateSource = readFileSync(path.join(process.cwd(), 'resources/views/article/templates/archive', template), 'utf8');
		assert.match(templateSource, /data-article-vue-list-content/);
		assert.match(templateSource, /data-article-vue-control-slot/);
		assert.match(templateSource, /data-article-vue-list-state-slot/);
	}

	assert.match(archive, /data-article-ssr v-once/);
	assert.match(source, /replaceListHtml/);
	assert.match(pagination, /article-pagination--ssr/);
	assert.match(archive, /<Teleport/);
	assert.match(archive, /to="\[data-article-vue-control-slot\]"/);
	assert.match(archive, /to="\[data-article-vue-list-state-slot\]"/);
	assert.match(archive, /article-vue-list-loading text-center p-5/);
	assert.match(css, /is-loading-list[^}]*data-article-vue-list-content/);
	assert.match(css, /\.article-pagination--vue/);
	assert.doesNotMatch(css, /article-vue-list-loader-host/);
	assert.doesNotMatch(css, /article-frontend-runtime__loading/);
});

test('archive pagination maps template options into a responsive Vue footer contract', () => {
	const options = loadOptions().article;
	const state = {
		paginationOptions: {
			show_total: false,
			position: 'center',
			frame: { enabled: false, border_color: '#123456', border_width: '2pt', radius: '1rem', background_color: '#ffffff' },
			padding: { enabled: true, desktop: { top: '1rem', right: '2px', bottom: '3%', left: '4pt' }, tablet: {}, mobile: {} },
			margin: { enabled: false, desktop: {}, tablet: {}, mobile: {} },
		},
	};

	assert.deepEqual([...options.computed.paginationClasses.call(state)], [
		'article-pagination--position-center',
		'article-pagination--without-total',
		'article-pagination--without-frame',
		'article-pagination--padding-enabled',
		'article-pagination--margin-default',
	]);
	assert.equal(options.computed.paginationStyle.call(state)['--article-pagination-padding-desktop-top'], '1rem');
	assert.equal(options.computed.paginationStyle.call(state)['--article-pagination-frame-border-width'], '2pt');

	const archive = readFileSync(path.join(process.cwd(), 'resources/views/article/archive.blade.php'), 'utf8');
	const pagination = readFileSync(path.join(process.cwd(), 'resources/views/article/templates/partials/pagination.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(archive, /data-template-options=/);
	assert.match(archive, /v-if="paginationOptions\.show_total"/);
	assert.match(archive, /:class="paginationClasses"/);
	assert.match(archive, /:style="paginationStyle"/);
	assert.match(pagination, /article-pagination--position-/);
	assert.match(pagination, /article-pagination--without-total/);
	assert.match(css, /article-pagination--position-center/);
	assert.match(css, /article-pagination--without-frame/);
	assert.match(css, /article-pagination-padding-desktop-top/);
});
