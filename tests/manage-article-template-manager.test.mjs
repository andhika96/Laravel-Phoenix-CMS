import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(process.cwd(), 'public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js');
const source = readFileSync(sourcePath, 'utf8');

function loadOptions(timerAdapter = {}) {
	const sandbox = {
		Vue: { createApp(options) { return { mount() { return options; } }; } },
		document: { getElementById() { return { dataset: {} }; } },
		window: {
			setTimeout(callback) { callback(); return 1; },
			clearTimeout() {},
			...timerAdapter,
		},
		URLSearchParams,
		console,
	};

	vm.createContext(sandbox);
	vm.runInContext(`${source}\n;globalThis.__options = ManageArticleTemplateVue3;globalThis.__unitControls = ArticleTemplateUnitControls;`, sandbox, { filename: sourcePath });
	sandbox.__options.__unitControls = sandbox.__unitControls;

	return sandbox.__options;
}

test('manager selection changes only the active surface and rebuilds the live preview URL', () => {
	const options = loadOptions();
	const state = {
		surface: 'archive',
		draft: { archive_template: 'minimal-reading-list', detail_template: 'focused-reader', archive_per_page: 12 },
		activeTemplates: { 'mosaic-magazine': { label: 'Mosaic Magazine' } },
		previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
		previewUrl: '',
		rebuildPreview: options.methods.rebuildPreview,
		get activeTemplateKey() { return this.surface === 'detail' ? this.draft.detail_template : this.draft.archive_template; },
	};

	options.methods.selectTemplate.call(state, 'mosaic-magazine');

	assert.equal(state.draft.archive_template, 'mosaic-magazine');
	assert.equal(state.draft.detail_template, 'focused-reader');
	assert.equal(state.previewUrl, '/manage_article/templates/preview/archive/mosaic-magazine');
});

test('manager passes the active CMS accent into the isolated preview and refreshes it when the theme changes', () => {
	const options = loadOptions();
	const state = {
		surface: 'archive',
		draft: { archive_template: 'minimal-reading-list', detail_template: 'focused-reader', archive_per_page: 12 },
		previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
		previewUrl: '',
		previewLoading: false,
		previewLoadTimer: null,
		activeThemeColor() { return '#FF5733'; },
		get activeTemplateKey() { return this.draft.archive_template; },
	};

	options.methods.rebuildPreview.call(state);

	assert.equal(state.previewUrl, '/manage_article/templates/preview/archive/minimal-reading-list?theme_color=%23FF5733');
	assert.match(source, /getComputedStyle\(document\.documentElement\)\.getPropertyValue\('--ph-theme-primary'\)/);
	assert.match(source, /theme_color/);
	assert.match(source, /MutationObserver/);
});

test('manager serializes the active template option draft into the iframe URL without saving it', () => {
	const options = loadOptions();
	const state = {
		surface: 'archive',
		draft: { archive_template: 'editorial-journal', detail_template: 'focused-reader' },
		previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
		previewUrl: '',
		previewLoading: false,
		previewLoadTimer: null,
		activeThemeColor() { return ''; },
		activeTemplateOptions() { return { grid: { desktop: 4, tablet: 3, mobile: 2 } }; },
		get activeTemplateKey() { return this.draft.archive_template; },
	};

	options.methods.rebuildPreview.call(state);

	const url = new URL(`https://example.test${state.previewUrl}`);
	assert.deepEqual(JSON.parse(url.searchParams.get('template_options')), { grid: { desktop: 4, tablet: 3, mobile: 2 } });
});

test('applying modal options updates only the active draft and rebuilds the preview', () => {
	const options = loadOptions();
	const state = {
		optionsModal: { key: 'editorial-journal', surface: 'archive', value: { grid: { desktop: 4, tablet: 3, mobile: 2 } } },
		draft: { archive_template_options: {}, detail_template_options: {} },
		cloneOptions: options.methods.cloneOptions,
		rebuildPreview() { this.previewRebuilt = true; },
		closeTemplateOptions() { this.modalClosed = true; },
	};

	options.methods.applyTemplateOptions.call(state);

	assert.equal(JSON.stringify(state.draft.archive_template_options['editorial-journal']), JSON.stringify({ grid: { desktop: 4, tablet: 3, mobile: 2 } }));
	assert.equal(state.previewRebuilt, true);
	assert.equal(state.modalClosed, true);
});

test('an unsaved preview selection is selected, not the persisted default', () => {
	const options = loadOptions();
	const state = {
		surface: 'archive',
		draft: { archive_template: 'mosaic-magazine', detail_template: 'focused-reader' },
		saved: { archive_template: 'minimal-reading-list', detail_template: 'focused-reader' },
	};

	assert.equal(options.methods.isPersistedDefault.call(state, 'mosaic-magazine'), false);
	assert.equal(options.methods.isPersistedDefault.call(state, 'minimal-reading-list'), true);
});

test('manager source keeps the selected visual direction and a real preview iframe', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');

	assert.match(view, /archiveTemplates/);
	assert.match(view, /detailTemplates/);
	assert.match(view, /<iframe/);
	assert.match(view, /Archive Templates/);
	assert.match(view, /Detail Templates/);
	assert.match(view, /isPersistedDefault\(key\)/);
	assert.match(view, /data-payload="\{\{ json_encode/);
	assert.doesNotMatch(view, /e\(json_encode/);
	assert.doesNotMatch(view, /@\{\{[^}]*\bt\s*\(/);
});

test('manager cards use template preview thumbnails with a local placeholder fallback', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');

	assert.match(view, /article-template-card__thumbnail/);
	assert.match(view, /:src="template\.preview_image"/);
	assert.match(view, /v-on:error="usePlaceholderThumbnail"/);
	assert.doesNotMatch(view, /@error="usePlaceholderThumbnail"/);
	assert.match(view, /data-placeholder-thumbnail=/);
	assert.match(view, /article-template-card__best-for/);
	assert.match(view, /template\.best_for/);
});

test('device preview uses fixed virtual viewports and scales the full frame to fit', () => {
	const options = loadOptions();
	const state = {
		activeDevice: { width: 1440, height: 900 },
		previewGutter: 48,
		previewMaxHeight: 640,
		deviceScale: 1,
		$refs: { previewViewport: { clientWidth: 1200 } },
	};

	options.methods.fitPreview.call(state);

	assert.equal(state.deviceScale, 640 / 900);
	assert.equal(options.computed.deviceStageStyle.call(state).width, '1024px');
	assert.equal(options.computed.deviceStageStyle.call(state).height, '640px');
	assert.equal(options.computed.deviceFrameStyle.call(state).width, '1440px');
	assert.equal(options.computed.deviceFrameStyle.call(state).transform, `scale(${640 / 900})`);
});

test('device selection changes viewport profile and refits the preview', () => {
	const options = loadOptions();
	const state = {
		surface: 'detail',
		device: 'desktop',
		deviceProfiles: { desktop: {}, mobile: {} },
		draft: { archive_template: 'minimal-reading-list', detail_template: 'focused-reader' },
		refitCalled: false,
		fitPreview() { this.refitCalled = true; },
		$nextTick(callback) { callback(); },
	};

	options.methods.selectDevice.call(state, 'mobile');

	assert.equal(state.device, 'mobile');
	assert.equal(state.refitCalled, true);
	assert.equal(state.surface, 'detail');
	assert.equal(state.draft.detail_template, 'focused-reader');
});

test('manager source exposes a labelled device stage instead of max-width-only iframes', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(view, /article-template-manager__device-stage/);
	assert.match(view, /deviceStageStyle/);
	assert.match(view, /activeDevice\.label/);
	assert.match(css, /\.article-template-manager__device-stage/);
	assert.match(css, /transform-origin: top left/);
	assert.doesNotMatch(css, /max-width: 834px/);
	assert.doesNotMatch(css, /max-width: 390px/);
});

test('template selection displays a loading state until the iframe has loaded the new preview', () => {
	const options = loadOptions();
	const state = {
		surface: 'archive',
		draft: { archive_template: 'minimal-reading-list', detail_template: 'focused-reader' },
		activeTemplates: { 'mosaic-classic': { label: 'Mosaic Classic' } },
		previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
		previewUrl: '',
		previewLoading: false,
		previewLoadTimer: null,
		get activeTemplateKey() { return this.draft.archive_template; },
	};
	state.rebuildPreview = options.methods.rebuildPreview;

	options.methods.selectTemplate.call(state, 'mosaic-classic');

	assert.equal(state.previewLoading, true);
	assert.equal(state.previewUrl, '/manage_article/templates/preview/archive/mosaic-classic');
	options.methods.onPreviewLoad.call(state);
	assert.equal(state.previewLoading, false);

	const timers = [];
	const timedOptions = loadOptions({
		setTimeout(callback, delay) { timers.push({ callback, delay }); return timers.length; },
		clearTimeout() {},
	});
	const timedState = { previewLoading: true, previewLoadTimer: null };
	timedOptions.methods.onPreviewLoad.call(timedState);
	assert.equal(timedState.previewLoading, true);
	assert.equal(timers[0].delay, 180);
	timers[0].callback();
	assert.equal(timedState.previewLoading, false);
});

test('manager source exposes an accessible preview-loading overlay bound to iframe load', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(view, /article-template-manager__preview-loading/);
	assert.match(view, /v-on:load="onPreviewLoad"/);
	assert.match(view, /:aria-busy="previewLoading/);
	assert.match(css, /\.article-template-manager__preview-loading/);
});

test('manager exposes an Add User-style template options modal with draft-only apply behavior', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(source, /openTemplateOptions/);
	assert.match(source, /applyTemplateOptions/);
	assert.match(source, /template_options/);
	assert.match(source, /archive_template_options/);
	assert.match(source, /detail_template_options/);
	assert.match(view, /modalArticleTemplateOptions/);
	assert.match(view, /Template Options/);
	assert.match(view, /modal-dialog-centered/);
	assert.match(view, /Apply changes/);
	assert.match(css, /\.article-template-options-modal/);
});

test('template option dimensions use the requested unit allowlists with deterministic linked-side behavior', () => {
	const controls = loadOptions().__unitControls;
	const box = { top: '1rem', right: '2px', bottom: '3%', left: '4pt' };

	assert.deepEqual([...controls.units('spacing')], ['px', 'em', 'rem', '%', 'pt']);
	assert.deepEqual([...controls.units('border')], ['px', 'em', 'rem', 'pt']);
	assert.deepEqual(JSON.parse(JSON.stringify(controls.parse('1.25rem', '0px', 'spacing'))), { value: 1.25, unit: 'rem' });
	assert.equal(controls.step('rem'), 0.01);
	assert.equal(controls.step('pt'), 1);
	assert.equal(controls.withUnit('16px', '%', 'border'), '16px');

	controls.setBoxValue(box, 'top', '1.25', true, 'spacing');
	assert.deepEqual(JSON.parse(JSON.stringify(box)), { top: '1.25rem', right: '1.25rem', bottom: '1.25rem', left: '1.25rem' });

	controls.setBoxUnit(box, 'pt', 'spacing');
	assert.deepEqual(JSON.parse(JSON.stringify(box)), { top: '1.25pt', right: '1.25pt', bottom: '1.25pt', left: '1.25pt' });

	controls.setBoxValue(box, 'left', '10', false, 'spacing');
	assert.deepEqual(JSON.parse(JSON.stringify(box)), { top: '1.25pt', right: '1.25pt', bottom: '1.25pt', left: '10pt' });
});

test('template options modal exposes only structured archive and detail styling controls', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const styling = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(view, /options-styling/);
	assert.match(styling, /Thumbnail/);
	assert.match(styling, /Pagination/);
	assert.match(styling, /Article title tag/);
	assert.match(styling, /Archive shell/);
	assert.match(styling, /Detail shell/);
	assert.match(styling, /article-template-unit-control/);
	assert.match(styling, /article-template-box-control/);
	assert.doesNotMatch(styling, /custom css/i);
	assert.doesNotMatch(styling, /custom class/i);
	assert.match(css, /\.article-template-unit-control/);
	assert.match(css, /\.article-template-box-control/);
});

test('template options uses the local Page Builder Coloris pattern and equal box-control geometry', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const styling = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(view, /assets\/vendor\/coloris\/coloris\.min\.css/);
	assert.match(view, /assets\/vendor\/coloris\/coloris\.min\.js/);
	assert.match(source, /initColorisPicker/);
	assert.match(source, /theme:\s*'pill'/);
	assert.match(source, /formatToggle:\s*true/);
	assert.match(source, /closeButton:\s*true/);
	assert.match(styling, /article-template-coloris/);
	assert.doesNotMatch(styling, /type="color"/);
	assert.match(css, /--article-template-control-height/);
	assert.match(css, /article-template-box-control__inputs > label \.form-control[\s\S]*?height: var\(--article-template-control-height\)/);
	assert.match(css, /article-template-box-control__link[\s\S]*?height: var\(--article-template-control-height\)/);
});
