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
		buildPreviewUrl: options.methods.buildPreviewUrl,
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
		buildPreviewUrl: options.methods.buildPreviewUrl,
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
		buildPreviewUrl: options.methods.buildPreviewUrl,
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
		rebuildPreview() { this.previewRebuilt = true; },
		$nextTick(callback) { callback(); },
	};

	options.methods.selectDevice.call(state, 'mobile');

	assert.equal(state.device, 'mobile');
	assert.equal(state.refitCalled, true);
	assert.equal(state.previewRebuilt, true);
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

test('live preview stage wrappers stay square while template content keeps its own frame settings', () => {
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const stageRules = [
		css.match(/\.article-template-manager__device-stage\s*\{([^}]*)\}/s)?.[1],
		css.match(/\.article-template-options-preview__stage\s*\{([^}]*)\}/s)?.[1],
	];

	assert.ok(stageRules.every(Boolean));
	assert.ok(stageRules.every((rule) => !/border-radius\s*:/.test(rule)));
	assert.doesNotMatch(css, /\.article-template-manager__device-stage iframe\s*\{[^}]*border-radius\s*:/s);
	assert.doesNotMatch(css, /\.article-template-options-preview__stage iframe\s*\{[^}]*border-radius\s*:/s);
	assert.match(css, /\.article-template-options-modal \.modal-content\s*\{[\s\S]*?border-radius:\s*1rem/);
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
		buildPreviewUrl: options.methods.buildPreviewUrl,
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
	assert.match(view, /@\{\{ copy\.loadingPreview \}\}/);
	assert.doesNotMatch(view, /(?<!@)\{\{\s*copy\.loadingPreview\s*\}\}/);
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

test('border radius control follows the Page Builder four-corner linked-unit behavior', () => {
	const controls = loadOptions().__unitControls;

	assert.deepEqual(JSON.parse(JSON.stringify(controls.radiusValues('4px 8px 12px 16px', '1rem', 'radius').values.map((item) => item.value))), [4, 8, 12, 16]);
	assert.deepEqual(JSON.parse(JSON.stringify(controls.radiusValues('4px 8px', '1rem', 'radius').values.map((item) => item.value))), [4, 8, 4, 8]);
	assert.equal(controls.setRadiusValue('4px 8px 12px 16px', 1, '20', false, 'radius'), '4px 20px 12px 16px');
	assert.equal(controls.setRadiusValue('4px 8px 12px 16px', 2, '20', true, 'radius'), '20px 20px 20px 20px');
	assert.equal(controls.setRadiusUnit('4px 8px 12px 16px', 'rem', 'radius'), '4rem 8rem 12rem 16rem');
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
	assert.match(source, /selectOptionsDevice/);
	assert.doesNotMatch(styling, /article-template-device-tabs--compact/);
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

test('Pagination options expose named models and independent desktop/tablet/mobile ranges', () => {
	const styling = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const managerSource = readFileSync(path.join(process.cwd(), 'public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js'), 'utf8');
	const css = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(styling, /Pagination model/);
	assert.match(styling, /Minimal Underline/);
	assert.match(styling, /Classic Boxed/);
	assert.match(styling, /Soft Highlight/);
	assert.match(styling, /paginationRangeValue\('desktop'\)/);
	assert.match(styling, /paginationRangeValue\('tablet'\)/);
	assert.match(styling, /paginationRangeValue\('mobile'\)/);
	assert.match(managerSource, /options\.pagination\.type/);
	assert.match(managerSource, /options\.pagination\.range/);
	assert.match(css, /article-template-pagination-range__fields[\s\S]*?grid-template-columns:\s*repeat\(3/);
});

test('Archive toolbar search exposes model, gap, Font Awesome icon, and state color controls', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const managerCss = readFileSync(path.join(process.cwd(), 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(view, /Search model/);
	assert.match(view, /Attached Classic/);
	assert.match(view, /Soft Field/);
	assert.match(view, /Minimal Underline/);
	assert.match(view, /Search button gap/);
	assert.match(view, /optionsModal\.value\.toolbar\.search\.icon/);
	assert.match(view, /button_hover_background_color/);
	assert.match(view, /button_active_background_color/);
	assert.match(managerCss, /article-template-search-style-fields/);
});

test('template options modal exposes conditional vertical navigation and a mobile section picker', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');

	assert.match(view, /article-template-options-nav/);
	assert.match(view, /role="tablist"/);
	assert.match(view, /aria-selected/);
	assert.match(view, /aria-controls/);
	assert.match(view, /role="tabpanel"/);
	assert.match(view, /article-template-options-section-picker/);
	assert.match(view, /data-bs-backdrop="static"/);
	assert.match(view, /data-bs-keyboard="false"/);
	assert.match(source, /optionSections\(\)/);
	assert.match(source, /setOptionsSection/);
	assert.match(source, /handleOptionsTabKeydown/);
});

test('option sections follow the active surface and template schema', () => {
	const options = loadOptions();
	const state = {
		surface: 'archive',
		activeTemplateKey: 'minimal-reading-list',
		optionsModal: {
			value: {
				post_list: { item_gap: '0.75rem' },
				sidebar: { enabled: true },
				grid: null,
			},
		},
	};

	assert.deepEqual(JSON.parse(JSON.stringify(options.computed.optionSections.call(state).map((section) => section.key))), [
		'header', 'toolbar', 'post-list', 'sidebar', 'thumbnail', 'pagination', 'article-title', 'shell',
	]);

	state.activeTemplateKey = 'editorial-journal';
	state.optionsModal.value = { grid: { desktop: 3, tablet: 2, mobile: 1 } };
	assert.deepEqual(JSON.parse(JSON.stringify(options.computed.optionSections.call(state).map((section) => section.key))), [
		'header', 'toolbar', 'grid', 'thumbnail', 'pagination', 'article-title', 'shell',
	]);

	state.surface = 'detail';
	state.activeTemplateKey = 'focused-reader';
	state.optionsModal.value = {};
	assert.deepEqual(JSON.parse(JSON.stringify(options.computed.optionSections.call(state).map((section) => section.key))), ['header', 'shell']);
});

test('modal preview uses the modal clone, debounces edits, and keeps the page draft untouched', () => {
	const timers = [];
	const options = loadOptions({
		setTimeout(callback, delay) { timers.push({ callback, delay }); return timers.length; },
		clearTimeout() {},
	});
	const state = {
		optionsModal: {
			surface: 'archive',
			key: 'minimal-reading-list',
			value: { header: { title: { text: 'Modal draft' } } },
		},
		draft: { archive_template_options: { 'minimal-reading-list': { header: { title: { text: 'Saved draft' } } } } },
		previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
		modalPreviewUrl: '',
		modalPreviewLoading: false,
		modalPreviewError: '',
		modalPreviewTimer: null,
		modalPreviewTimeoutTimer: null,
		modalPreviewRequestSequence: 0,
		activeThemeColor() { return ''; },
		buildPreviewUrl: options.methods.buildPreviewUrl,
		clearModalPreviewTimers: options.methods.clearModalPreviewTimers,
	};

	options.methods.rebuildModalPreview.call(state);
	const url = new URL(`https://example.test${state.modalPreviewUrl}`);
	assert.deepEqual(JSON.parse(url.searchParams.get('template_options')), state.optionsModal.value);
	assert.equal(state.draft.archive_template_options['minimal-reading-list'].header.title.text, 'Saved draft');
	assert.equal(state.modalPreviewLoading, true);
	assert.equal(state.modalPreviewRequestSequence, 1);

	options.methods.scheduleModalPreview.call(state);
	assert.equal(timers.at(-1).delay, 350);
});

test('page and modal Minimal Reading List previews share the same read-only renderer endpoint', () => {
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const controller = readFileSync(path.join(process.cwd(), 'app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php'), 'utf8');
	const options = loadOptions();
	const state = {
		previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
		activeThemeColor() { return ''; },
	};
	const draft = { toolbar: { search: { enabled: true, position: 'center' } }, post_list: { item_gap: '2rem' } };
	const pageUrl = options.methods.buildPreviewUrl.call(state, 'archive', 'minimal-reading-list', draft);
	const modalUrl = options.methods.buildPreviewUrl.call(state, 'archive', 'minimal-reading-list', JSON.parse(JSON.stringify(draft)));
	const mobileUrl = options.methods.buildPreviewUrl.call(state, 'archive', 'minimal-reading-list', draft, 'mobile');

	assert.equal(new URL(`https://example.test${pageUrl}`).pathname, '/manage_article/templates/preview/archive/minimal-reading-list');
	assert.equal(new URL(`https://example.test${modalUrl}`).pathname, new URL(`https://example.test${pageUrl}`).pathname);
	assert.equal(new URL(`https://example.test${mobileUrl}`).searchParams.get('preview_device'), 'mobile');
	assert.match(view, /:src="previewUrl"/);
	assert.match(view, /:src="modalPreviewUrl"/);
	assert.match(source, /this\.previewUrl = this\.buildPreviewUrl\(this\.surface, this\.activeTemplateKey, templateOptions, this\.device\)/);
	assert.match(source, /this\.modalPreviewUrl = this\.buildPreviewUrl\(modal\.surface, modal\.key, modal\.value, this\.optionsDevice\)/);
	assert.match(controller, /return view\('manage_article\.templates\.preview'/);
	assert.match(controller, /private function previewOptions/);
});

test('modal session starts on Header content and isolates every edit from its source object', () => {
	const options = loadOptions();
	const sourceOptions = { header: { title: { text: 'Original' } } };
	const session = options.methods.createOptionsSession.call({ cloneOptions: options.methods.cloneOptions }, sourceOptions, 'archive', 'minimal-reading-list');

	assert.equal(session.section, 'header');
	assert.equal(session.view, 'settings');
	assert.equal(session.dirty, false);
	session.value.header.title.text = 'Changed only in modal';
	assert.equal(sourceOptions.header.title.text, 'Original');
});

test('modal preview uses the fixed virtual device profile when fitting its viewport', () => {
	const options = loadOptions();
	const state = {
		optionsDevice: 'tablet',
		deviceProfiles: {
			desktop: { width: 1440, height: 900 },
			tablet: { width: 834, height: 1112 },
		},
		modalPreviewScale: 1,
		modalPreviewGutter: 32,
		modalPreviewMaxHeight: 620,
		$refs: { optionsPreviewViewport: { clientWidth: 700 } },
		get optionsPreviewDevice() { return this.deviceProfiles[this.optionsDevice]; },
	};

	options.methods.fitOptionsPreview.call(state);

	assert.equal(state.modalPreviewScale, 620 / 1112);
	assert.equal(options.computed.optionsPreviewStageStyle.call(state).width, `${Math.round(834 * (620 / 1112))}px`);
	assert.equal(options.computed.optionsPreviewFrameStyle.call(state).height, '1112px');
});

test('modal dismissal exposes an inline dirty confirmation and retryable preview lifecycle', () => {
	const options = loadOptions();
	const view = readFileSync(path.join(process.cwd(), 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const state = {
		optionsModal: { dirty: true, dismissOpen: false },
		closeTemplateOptions: options.methods.closeTemplateOptions,
	};

	options.methods.requestCloseTemplateOptions.call(state);
	assert.equal(state.optionsModal.dismissOpen, true);
	options.methods.keepEditing.call(state);
	assert.equal(state.optionsModal.dismissOpen, false);

	assert.match(source, /modalPreviewTimeoutTimer/);
	assert.match(source, /retryModalPreview/);
	assert.match(source, /clearModalPreviewTimers/);
	assert.match(source, /auth\/login/);
	assert.match(source, /contentDocument/);
	assert.match(view, /Keep editing/);
	assert.match(view, /Discard changes/);
	assert.match(view, /Retry/);
	assert.match(view, /v-on:error="onModalPreviewError"/);
	assert.doesNotMatch(view, /@error\s*=\s*"onModalPreviewError"/);
});

test('hidden modal lifecycle cancels preview timers, disconnects the observer, and clears the session', () => {
	const options = loadOptions();
	const observer = { disconnected: false, disconnect() { this.disconnected = true; } };
	const state = {
		optionsModalTriggerId: 'article-template-options-trigger',
		optionsModal: { key: 'minimal-reading-list', surface: 'archive', value: { header: {} } },
		modalPreviewTimer: 1,
		modalPreviewTimeoutTimer: 2,
		modalPreviewResizeObserver: observer,
		modalPreviewUrl: '/preview',
		modalPreviewError: 'error',
		clearModalPreviewTimers: options.methods.clearModalPreviewTimers,
		$nextTick(callback) { callback(); },
	};

	options.methods.onOptionsModalHidden.call(state);

	assert.equal(state.optionsModal.value, null);
	assert.equal(state.modalPreviewUrl, '');
	assert.equal(state.modalPreviewError, '');
	assert.equal(state.modalPreviewResizeObserver, null);
	assert.equal(observer.disconnected, true);
});

test('modal preview treats an authentication redirect as an error instead of a successful load', () => {
	const options = loadOptions();
	const state = {
		modalPreviewUrl: '/manage_article/templates/preview/archive/minimal-reading-list',
		modalPreviewLoading: true,
		modalPreviewError: '',
		modalPreviewTimer: null,
		modalPreviewTimeoutTimer: 1,
		clearModalPreviewTimers: options.methods.clearModalPreviewTimers,
	};

	options.methods.onModalPreviewLoad.call(state, {
		target: {
			getAttribute() { return state.modalPreviewUrl; },
			contentWindow: { location: { pathname: '/auth/login' } },
			contentDocument: { querySelector() { return null; } },
		},
	});

	assert.equal(state.modalPreviewLoading, false);
	assert.match(state.modalPreviewError, /unavailable/i);
});

test('modal preview URL stays within a practical GET budget for the largest draft payload', () => {
	const options = loadOptions();
	const state = {
		previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
		activeThemeColor() { return '#123456'; },
	};
	const largestDraft = {
		header: {
			eyebrow: { enabled: true, text: 'x'.repeat(160) },
			title: { enabled: true, text: 'x'.repeat(160) },
			description: { enabled: true, text: 'x'.repeat(280) },
		},
		toolbar: { search: { enabled: true, position: 'center' }, category: { enabled: true, position: 'right', mode: 'button-list' } },
		post_list: { item_gap: '30rem' },
		sidebar: { enabled: true, categories: { enabled: true, position: 'sticky' }, popular: { enabled: true, position: 'sticky' } },
		thumbnail: { mode: 'asset', fit: 'contain', background_color: '#123456', frame: { enabled: true, border_color: '#654321', border_width: '400pt', radius: '30rem', background_color: '#abcdef' } },
		pagination: { show_total: true, position: 'center', frame: { enabled: true, border_color: '#123456', border_width: '400pt', radius: '30rem', background_color: '#abcdef' }, padding: { enabled: true, desktop: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, tablet: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, mobile: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' } }, margin: { enabled: true, desktop: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, tablet: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, mobile: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' } } },
		article_title: { tag: 'h6' },
		shell: { padding: { enabled: true, desktop: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, tablet: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, mobile: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' } }, margin: { enabled: true, desktop: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, tablet: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' }, mobile: { top: '30rem', right: '30rem', bottom: '30rem', left: '30rem' } }, frame: { enabled: true, border_color: '#123456', border_width: '400pt', radius: '30rem', background_color: '#abcdef' } },
	};

	const previewUrl = options.methods.buildPreviewUrl.call(state, 'archive', 'minimal-reading-list', largestDraft);

	assert.ok(previewUrl.length < 8000, `preview URL unexpectedly large: ${previewUrl.length}`);
});
