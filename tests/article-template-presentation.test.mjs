import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();

test('article frontend typography inherits CMS responsive typography tokens', () => {
	const frontendCss = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');
	const managerCss = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const archiveView = readFileSync(path.join(root, 'resources/views/article/archive.blade.php'), 'utf8');
	const detailView = readFileSync(path.join(root, 'resources/views/article/detail.blade.php'), 'utf8');
	const previewView = readFileSync(path.join(root, 'resources/views/manage_article/templates/preview.blade.php'), 'utf8');

	assert.match(frontendCss, /var\(--ph-adaptive-font-size/);
	assert.match(frontendCss, /var\(--ph-fmv2-rfs-h1/);
	assert.match(managerCss, /var\(--ph-adaptive-font-size/);
	assert.doesNotMatch(frontendCss, /Georgia/);
	assert.doesNotMatch(frontendCss, /font-size:[^;}]*4\.5rem/);
	assert.doesNotMatch(managerCss, /font-size:1\.75rem/);

	for (const source of [archiveView, detailView, previewView]) {
		assert.match(source, /theme-responsive-typography\.css/);
		assert.match(source, /SiteTypography::class/);
	}
	assert.doesNotMatch(previewView, /font-size:clamp/);
});

test('Article title and short description use separate responsive CMS-bound scales', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(css, /--article-title-size: min\(var\(--ph-fmv2-rfs-h1/);
	assert.match(css, /--article-description-size: min\(var\(--ph-fmv2-rfs-h4/);
	assert.match(css, /--article-title-size: min\(var\(--ph-fmv2-rfs-h1, 2\.5rem\), 1\.62em\)/);
	assert.match(css, /--article-description-size: min\(var\(--ph-fmv2-rfs-h4, 1\.25rem\), 1\.22em\)/);
	assert.match(css, /@media \(max-width: 991\.98px\)[\s\S]*?--article-title-size: min\(var\(--ph-fmv2-rfs-h1, 2\.25rem\), 1\.4em\)/);
	assert.match(css, /@media \(max-width: 575\.98px\)[\s\S]*?--article-title-size: min\(var\(--ph-fmv2-rfs-h1, 2rem\), 1\.3em\)/);
	assert.match(css, /\.article-page-heading h1,[\s\S]*?font-size: var\(--article-title-size\)/);
	assert.match(css, /\.article-page-heading p:not\(.article-eyebrow\)[\s\S]*?font-size: var\(--article-description-size\)/);
	assert.match(css, /\.article-detail__dek[\s\S]*?font-size: var\(--article-description-size\)/);
	assert.match(css, /@media \(max-width: 575\.98px\)[\s\S]*?--article-title-size:/);
});

test('all Article templates provide an image fallback for empty thumbnails', () => {
	for (const relativePath of [
		'archive/minimal-reading-list.blade.php',
		'archive/mosaic-magazine.blade.php',
		'archive/editorial-journal.blade.php',
		'archive/mosaic-classic.blade.php',
		'archive/balanced-card-grid.blade.php',
		'detail/focused-reader.blade.php',
		'detail/editorial-feature.blade.php',
		'detail/knowledge-toc.blade.php',
	]) {
		const source = readFileSync(path.join(root, 'resources/views/article/templates', relativePath), 'utf8');

		assert.match(source, /article-image-placeholder\.svg/);
	}
});

test('archive cards use shared media, title, excerpt, and equal-height presentation contracts', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(css, /\.article-media-frame/);
	assert.match(css, /border-radius: 1rem/);
	assert.match(css, /\.article-title-clamp/);
	assert.match(css, /-webkit-line-clamp: 2/);
	assert.match(css, /\.article-excerpt-clamp/);
	assert.match(css, /-webkit-line-clamp: 3/);
	assert.match(css, /\.article-card__body[\s\S]*display: flex/);
});

test('detail templates use the editorial reading and neighbor-navigation contracts', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(css, /\.article-detail__header/);
	assert.match(css, /\.article-rich-content blockquote/);
	assert.match(css, /\.article-detail-navigation/);
	assert.match(css, /\.article-feature-hero__overlay/);
});

test('Article and manager accents inherit the active CMS primary color token', () => {
	const frontendCss = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');
	const managerCss = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const themeSync = readFileSync(path.join(root, 'public/assets/js/article/article-theme-color-sync-2026.js'), 'utf8');
	const archive = readFileSync(path.join(root, 'resources/views/article/archive.blade.php'), 'utf8');
	const detail = readFileSync(path.join(root, 'resources/views/article/detail.blade.php'), 'utf8');

	assert.match(frontendCss, /--article-accent: var\(--ph-theme-primary/);
	assert.match(managerCss, /--article-template-accent: var\(--ph-theme-primary/);
	assert.match(frontendCss, /color-mix\(in srgb, var\(--article-accent\)/);
	assert.match(managerCss, /color-mix\(in srgb, var\(--article-template-accent\)/);
	assert.match(themeSync, /localStorage\.getItem\('theme-color'\)/);
	assert.match(themeSync, /URLSearchParams/);
	assert.match(themeSync, /theme_color/);
	assert.match(themeSync, /--ph-theme-primary/);
	assert.match(themeSync, /storage/);
	assert.match(archive, /article-theme-color-sync-2026\.js/);
	assert.match(detail, /article-theme-color-sync-2026\.js/);
});

test('Archive thumbnails keep a cover fallback while allowing validated background or asset rendering', () => {
	const frontendCss = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');
	const media = readFileSync(path.join(root, 'resources/views/article/templates/partials/media-link.blade.php'), 'utf8');

	assert.match(frontendCss, /\.article-background-media/);
	assert.match(frontendCss, /background-size: var\(--article-thumbnail-fit, cover\)/);
	assert.match(frontendCss, /background-position: center/);
	assert.match(frontendCss, /background-repeat: no-repeat/);
	assert.match(frontendCss, /\.article-asset-media > img/);
	assert.match(frontendCss, /object-fit: var\(--article-thumbnail-fit, cover\)/);
	assert.match(media, /article-thumbnail-frame--custom/);

	for (const selector of [
		'\\.article-reading-list__media',
		'\\.article-mosaic-feature__media',
		'\\.article-editorial-lead__media',
		'\\.article-classic-lead__media',
		'\\.article-classic-sidebar__media',
	]) {
		assert.match(frontendCss, new RegExp(`${selector}\\s*\\{[^}]*background-color:`));
	}

	assert.match(frontendCss, /\.article-mosaic-card__media,[\s\S]*?\.article-editorial-card__media\s*\{[^}]*background-color:/);
});

test('thumbnail height is conditional to background mode and both media modes keep distinct rendering paths', () => {
	const styling = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const media = readFileSync(path.join(root, 'resources/views/article/templates/partials/media-link.blade.php'), 'utf8');
	const frontendCss = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(styling, /v-if="optionsModal\.value\.thumbnail\.mode === 'background'"/);
	assert.match(styling, /dimensionValue\('thumbnail\.height'\)/);
	assert.match(styling, /dimensionUnit\('thumbnail\.height'\)/);
	assert.match(media, /--article-thumbnail-height:/);
	assert.match(media, /@if\(\$thumbnailMode === 'asset'\)<img/);
	assert.match(frontendCss, /\.article-background-media\s*\{[\s\S]*?background-image:\s*var\(--article-media-image\)/);
	assert.match(frontendCss, /\.article-asset-media > img\s*\{[\s\S]*?object-fit:\s*var\(--article-thumbnail-fit, cover\)/);
	assert.match(frontendCss, /\.article-reading-list__media\.article-background-media,[\s\S]*?height:\s*var\(--article-thumbnail-height/);
});

test('tablet and mobile previews compact editorial spacing and retain a visible search action', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');
	const header = readFileSync(path.join(root, 'resources/views/article/templates/partials/archive-header.blade.php'), 'utf8');

	assert.match(header, /name="search"/);
	assert.match(header, /fa-search/);
	assert.match(css, /\.article-template-toolbar \.ph-btn-theme[\s\S]*?background: var\(--article-accent\)/);
	assert.match(css, /\.article-search-icon__label\s*\{[\s\S]*?display: inline/);
	assert.match(css, /\.article-search-icon button\s*\{[\s\S]*?align-self: stretch/);
	assert.match(css, /\.article-search-icon button\s*\{[\s\S]*?min-height: 44px/);
	assert.match(css, /@media \(max-width: 991\.98px\)\s*\{[\s\S]*?\.article-page-heading\s*\{[\s\S]*?margin-bottom: 1\.75rem/);
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-shell,[\s\S]*?width: min\(100% - 2rem, 1180px\)/);
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-reading-list__item\s*\{[\s\S]*?padding: 1\.25rem 0/);
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-editorial-lead\s*\{[\s\S]*?gap: 1\.25rem/);
});

test('Minimal Reading List uses distinct tablet and stacked mobile article-list layouts', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(css, /@media \(max-width: 991\.98px\)\s*\{[\s\S]*?\.article-page--reading-list \.article-reading-list__item\s*\{[\s\S]*?grid-template-columns:\s*minmax\(10rem, 13rem\)/);
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-page--reading-list \.article-reading-list__item\s*\{[\s\S]*?grid-template-columns:\s*1fr/);
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-page--reading-list \.article-reading-list__media[^}]*aspect-ratio:\s*16 \/ 9/);
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-page--reading-list \.article-reading-list__body\s*\{[\s\S]*?padding-top:\s*0/);
	assert.doesNotMatch(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?grid-template-columns:\s*6rem minmax\(0, 1fr\)/);
});

test('responsive Minimal Reading List mockup exposes focused desktop, tablet, and stacked mobile states', () => {
	const mockup = readFileSync(path.join(root, 'project-artifacts/mockups/20260905_article-minimal-reading-list-responsive/index.html'), 'utf8');

	assert.match(mockup, /data-device="desktop"/);
	assert.match(mockup, /data-device="tablet"/);
	assert.match(mockup, /data-device="mobile"/);
	assert.match(mockup, /@container \(max-width: 900px\)[\s\S]*?grid-template-columns: 176px minmax\(0, 1fr\)/);
	assert.match(mockup, /@container \(max-width: 560px\)[\s\S]*?grid-template-columns: 1fr/);
	assert.match(mockup, /@container \(max-width: 560px\)[\s\S]*?aspect-ratio: 16 \/ 9/);
});

test('Balanced Card Grid uses a named category select instead of a numeric category field', () => {
	const header = readFileSync(path.join(root, 'resources/views/article/templates/partials/archive-header.blade.php'), 'utf8');

	assert.match(header, /<select[^>]*name="category"/);
	assert.match(header, /articleCategories/);
	assert.doesNotMatch(header, /type="number"/);
});

test('archive and detail templates expose shared configurable headers and toolbar placement hooks', () => {
	for (const relativePath of [
		'archive/minimal-reading-list.blade.php',
		'archive/editorial-journal.blade.php',
		'archive/mosaic-magazine.blade.php',
		'archive/mosaic-classic.blade.php',
		'archive/balanced-card-grid.blade.php',
	]) {
		const source = readFileSync(path.join(root, 'resources/views/article/templates', relativePath), 'utf8');
		assert.match(source, /article\.templates\.partials\.archive-header/);
	}

	for (const relativePath of [
		'detail/focused-reader.blade.php',
		'detail/editorial-feature.blade.php',
		'detail/knowledge-toc.blade.php',
	]) {
		const source = readFileSync(path.join(root, 'resources/views/article/templates', relativePath), 'utf8');
		assert.match(source, /article\.templates\.partials\.detail-header-copy/);
	}

	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');
	assert.match(css, /\.article-template-toolbar__zone--left/);
	assert.match(css, /--article-template-grid-desktop/);
});

test('all archive list titles use a validated configurable heading partial with an H4 default and smaller category badges', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');
	const title = readFileSync(path.join(root, 'resources/views/article/templates/partials/archive-title.blade.php'), 'utf8');

	for (const relativePath of [
		'archive/minimal-reading-list.blade.php',
		'archive/editorial-journal.blade.php',
		'archive/mosaic-magazine.blade.php',
		'archive/mosaic-classic.blade.php',
		'archive/balanced-card-grid.blade.php',
	]) {
		const source = readFileSync(path.join(root, 'resources/views/article/templates', relativePath), 'utf8');
		assert.match(source, /article\.templates\.partials\.archive-title/);
		assert.doesNotMatch(source, /<h[1-6] class="article-title-clamp">/);
	}

	assert.match(title, /in_array\(\$titleTag, \['h1', 'h2', 'h3', 'h4', 'h5', 'h6'\]/);
	assert.match(title, /<h4 class="article-title-clamp">/);
	assert.match(title, /<h1 class="article-title-clamp">/);
	assert.match(title, /<h6 class="article-title-clamp">/);
	assert.match(css, /--article-list-title-size: min\(var\(--ph-fmv2-rfs-h4, 1\.25rem\), 1\.14em\)/);
	assert.match(css, /\.article-reading-list \.article-title-clamp,[\s\S]*?font-size: var\(--article-list-title-size\)/);
	assert.match(css, /\.article-mosaic-feature__body \.article-title-clamp,[\s\S]*?font-size: var\(--article-list-title-size\)/);
	assert.match(css, /\.article-chip\s*\{[\s\S]*?font-size: \.66em/);
});

test('template options revamp keeps modal regions independently scrollable and responsive', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const styling = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');

	assert.match(css, /article-template-options-layout/);
	assert.match(css, /article-template-options-modal \.modal-content[\s\S]*?flex-direction:\s*column/);
	assert.match(css, /article-template-options-modal \.modal-dialog[\s\S]*?height:\s*calc\(100dvh - 48px\)/);
	assert.match(css, /article-template-options-settings/);
	assert.match(css, /article-template-options-panel[\s\S]*?overflow-y:\s*auto/);
	assert.match(css, /article-template-options-preview/);
	assert.match(css, /min-height:\s*0/);
	assert.match(css, /overflow-y:\s*auto/);
	assert.match(css, /@media \(max-width: 1279\.98px\)/);
	assert.match(css, /@media \(max-width: 767\.98px\)/);
	assert.match(css, /prefers-reduced-motion/);
	assert.match(styling, /optionsModal\.section === 'thumbnail'/);
	assert.match(styling, /optionsModal\.section === 'pagination'/);
	assert.match(styling, /optionsModal\.section === 'shell'/);
});

test('settings panel keeps a constrained flex scroll chain after form density changes', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /\.article-template-options-settings\s*\{[^}]*overflow:\s*hidden/);
	assert.match(css, /\.article-template-options-panel\s*\{[^}]*height:\s*auto/);
	assert.match(css, /\.article-template-options-panel\s*\{[^}]*flex:\s*1 1 0%/);
	assert.match(css, /\.article-template-options-panel\s*\{[^}]*overflow-x:\s*hidden/);
});

test('template options modal places its sizing hook on the wrapper used by descendant selectors', () => {
	const view = readFileSync(path.join(root, 'resources/views/manage_article/templates/index.blade.php'), 'utf8');

	assert.match(view, /<div class="modal fade article-template-options-modal"[^>]*id="modalArticleTemplateOptions"/);
	assert.match(view, /<div class="modal-dialog ph-modal-dialog modal-dialog-centered">/);
	assert.doesNotMatch(view, /<div class="modal-dialog ph-modal-dialog modal-dialog-centered article-template-options-modal">/);
});

test('Header content panel follows the approved vertical field layout', () => {
	const view = readFileSync(path.join(root, 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(view, /article-template-header-field/);
	assert.match(view, /article-template-header-field__label/);
	assert.match(view, /A short label above the title/);
	assert.match(css, /article-template-header-field\s*\{/);
	assert.match(css, /article-template-header-field__label[\s\S]*?justify-content:\s*space-between/);
	assert.match(css, /article-template-header-field__control[\s\S]*?width:\s*100%/);
});

test('Template Options section headings use h5 semantics and the requested 1.8rem field gap', () => {
	const view = readFileSync(path.join(root, 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const styling = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const headingRows = `${view}\n${styling}`.match(/<div class="article-template-options-section__heading">[^\n]*/g) || [];

	assert.ok(headingRows.length >= 8);
	assert.ok(headingRows.every((heading) => heading.includes('<h5>') && !heading.includes('<strong>')));
	assert.match(css, /article-template-options-panel \.article-template-options-section__heading\s*\{[\s\S]*?margin-bottom:\s*1\.8rem/);
	assert.match(css, /article-template-options-panel \.article-template-options-section__heading h5[\s\S]*?margin:\s*0/);
});

test('all option form controls use the roomy full-width panel rhythm', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /article-template-options-panel \.article-template-reading-list-control \.form-select[\s\S]*?width:\s*100%/);
	assert.match(css, /article-template-options-panel \.article-template-unit-control[\s\S]*?grid-template-columns: minmax\(0, 1fr\) 5\.5rem/);
	assert.match(css, /article-template-options-panel \.article-template-frame-fields[\s\S]*?grid-template-columns:\s*1fr/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls[\s\S]*?display: grid/);
	assert.match(css, /article-template-options-panel \.article-template-option-row > \.form-switch[\s\S]*?flex-direction: row-reverse/);
	assert.match(css, /article-template-options-panel \.article-template-option-row > \.form-switch \.form-check-label[\s\S]*?margin-right: auto/);
});

test('Minimal Reading List category filter uses progressive disclosure and a native position select', () => {
	const view = readFileSync(path.join(root, 'resources/views/manage_article/templates/index.blade.php'), 'utf8');

	assert.match(view, /article-template-toolbar-option-controls--category/);
	assert.match(view, /article-template-category-position/);
	assert.match(view, /v-model="optionsModal\.value\.toolbar\.category\.position"/);
	assert.match(view, /v-if="optionsModal\.value\.toolbar\.category\.enabled"/);
	assert.match(view, /v-if="optionsModal\.value\.toolbar\[field\]\.enabled && \(field !== 'category' \|\| optionsModal\.value\.toolbar\.category\.enabled\)"/);
	assert.doesNotMatch(view, /!optionsModal\.value\.toolbar\.category\.enabled \|\| optionsModal\.value\.toolbar\.category\.mode === 'select'/);
});

test('Header content descriptions use a ten-row textarea on archive and custom detail fields', () => {
	const view = readFileSync(path.join(root, 'resources/views/manage_article/templates/index.blade.php'), 'utf8');
	const textareas = view.match(/<textarea\b[^>]*>/g) || [];

	assert.equal(textareas.length, 2);
	assert.equal(textareas.filter((textarea) => /field === 'description'/.test(textarea)).length, 1);
	assert.equal(textareas.filter((textarea) => /field !== 'title'/.test(textarea)).length, 1);
	assert.equal(textareas.filter((textarea) => /rows="10"/.test(textarea)).length, 2);
	assert.doesNotMatch(view, /<textarea\b[^>]*rows="3"/);
});

test('Archive toolbar search position buttons use the themed hover state and category controls stay left aligned', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /article-template-options-panel \.article-template-option-row--toolbar \.article-template-position \.btn:not\(\.btn-primary\):hover/);
	assert.match(css, /article-template-options-panel \.article-template-option-row--toolbar \.article-template-position \.btn:not\(\.btn-primary\):focus-visible/);
	assert.match(css, /article-template-options-panel \.article-template-option-row--toolbar \.article-template-position \.btn:not\(\.btn-primary\):hover[\s\S]*?background-color:\s*var\(--article-template-accent\)/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls--category[\s\S]*?text-align:\s*left/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls--category[\s\S]*?justify-content:\s*start/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls--category[\s\S]*?padding-left:\s*0/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls--category[\s\S]*?border-left:\s*0/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls--category \.article-template-reading-list-control[\s\S]*?justify-self:\s*stretch/);
});

test('teleported Template Options modal keeps the CMS accent token available to hover styles', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /\.article-template-options-modal\s*\{[\s\S]*?--article-template-accent:\s*var\(--ph-theme-primary,\s*#6542d7\)/);
});

test('list settings keep consistent bottom dividers across nested and compound option groups', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /article-template-options-panel \.article-template-header-field,[\s\S]*?article-template-options-panel \.article-template-option-row,[\s\S]*?article-template-options-panel \.article-template-sidebar-option,[\s\S]*?article-template-options-panel \.article-template-box-control[\s\S]*?border-bottom:\s*1px solid var\(--article-template-list-divider\)/);
	assert.match(css, /article-template-options-panel \.article-template-sidebar-option\s*\{[\s\S]*?padding-bottom:\s*1rem/);
	assert.doesNotMatch(css, /article-template-options-panel \.article-template-options-fields > \.article-template-option-row:last-child,[\s\S]*?border-bottom:\s*0/);
	assert.match(css, /article-template-options-panel \.article-template-box-control\s*\{[^}]*padding-left:\s*0[^}]*border-left:\s*0/);
});

test('Template Options V3 mockup mirrors the updated textarea, toolbar hover, and left category controls', () => {
	const mockup = readFileSync(path.join(root, 'project-artifacts/mockups/template-options-20260905/forms-v3/index.html'), 'utf8');

	assert.match(mockup, /\.segmented--toolbar button:hover:not\(\.is-active\)[\s\S]*?background: var\(--accent\)/);
	assert.match(mockup, /positionSegmented\('left', 'segmented--toolbar'\)/);
	assert.match(mockup, /conditional conditional--category/);
	assert.match(mockup, /positionSelect\('Left'\)/);
	assert.match(mockup, /compound\('Height', '9\.3', 'rem'\)/);
	assert.match(mockup, /<textarea class="textarea" rows="10"/);
	assert.match(mockup, /\.field-card \{[^}]*border-bottom: 1px solid #edf0f3/);
});

test('frame styling controls use one full-width row per field', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /article-template-options-panel \.article-template-frame-fields[\s\S]*?grid-template-columns:\s*1fr/);
});

test('border radius follows the Page Builder four-corner form-group pattern', () => {
	const partial = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const source = readFileSync(path.join(root, 'public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(partial, /article-template-radius-control/);
	assert.match(partial, /article-template-radius-fields/);
	assert.match(partial, /article-template-radius-link/);
	assert.match(partial, /radiusValue\(/);
	assert.match(partial, /setRadiusValue\(/);
	assert.match(partial, /setRadiusUnit\(/);
	assert.match(source, /radiusValues\(/);
	assert.match(source, /setRadiusValue\(/);
	assert.match(source, /setRadiusUnit\(/);
	assert.match(source, /radiusCorners:/);
	assert.match(css, /article-template-options-panel \.article-template-radius-fields[\s\S]*?grid-template-columns:/);
});

test('pagination button radius uses the four-corner form-group and chain link', () => {
	const partial = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const paginationSection = partial.match(/<section v-if="optionsModal\.section === 'pagination'[\s\S]*?<\/section>/)?.[0] ?? '';

	assert.match(paginationSection, /<div class="article-template-radius-control">[\s\S]*?Pagination button radius/);
	assert.match(paginationSection, /radiusUnit\('pagination\.item_radius'\)/);
	assert.match(paginationSection, /setRadiusUnit\('pagination\.item_radius'/);
	assert.match(paginationSection, /radiusValue\('pagination\.item_radius', index\)/);
	assert.match(paginationSection, /setRadiusValue\('pagination\.item_radius', index/);
	assert.match(paginationSection, /isRadiusLinked\('pagination\.item_radius'\)/);
	assert.match(paginationSection, /toggleRadiusLinked\('pagination\.item_radius'\)/);
	assert.doesNotMatch(paginationSection, /dimensionValue\('pagination\.item_radius'/);
	assert.doesNotMatch(paginationSection, /setDimensionValue\('pagination\.item_radius'/);
});

test('pagination radius and number gap occupy separate full-width rows', () => {
	const partial = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const paginationSection = partial.match(/<section v-if="optionsModal\.section === 'pagination'[\s\S]*?<\/section>/)?.[0] ?? '';

	assert.match(paginationSection, /class="article-template-radius-control"/);
	assert.match(paginationSection, /class="article-template-pagination-number-gap"/);
	assert.match(css, /article-template-pagination-style-fields > \.article-template-radius-control,[\s\S]*?article-template-pagination-number-gap[\s\S]*?grid-column:\s*1\s*\/\s*-1/);
});

test('Editorial Journal manager controls stay scoped and expose the complete new options surface', () => {
	const styling = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const manager = readFileSync(path.join(root, 'public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js'), 'utf8');
	const frontend = readFileSync(path.join(root, 'resources/views/article/templates/archive/editorial-journal.blade.php'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(styling, /optionsModal\.section === 'editorial-journal'/);
	assert.match(styling, /Show divider/);
	assert.match(styling, /Spacing with divider/);
	assert.match(styling, /Spacing without divider/);
	assert.match(styling, /Edge-to-edge/);
	assert.match(styling, /Border type/);
	assert.match(styling, /Card background/);
	assert.match(styling, /Choose Image/);
	assert.match(styling, /Card height/);
	assert.match(styling, /Show Read More/);
	assert.match(manager, /editorial_journal/);
	assert.match(manager, /chooseEditorialJournalBackground/);
	assert.match(frontend, /article-editorial-lead--\{\{ \$dividerEnabled \? 'with' : 'without' \}\}-divider/);
	assert.match(frontend, /article-editorial-card__body/);
	assert.match(frontend, /article-editorial-read-more/);
	assert.match(css, /article-editorial-card--thumbnail-edge/);
	assert.match(css, /article-editorial-card--height-fixed/);
	assert.match(css, /article-editorial-read-more--center/);
});

test('spacing groups mirror the Page Builder form-group hierarchy with a shared unit header', () => {
	const partial = readFileSync(path.join(root, 'resources/views/manage_article/templates/partials/options-styling.blade.php'), 'utf8');
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(partial, /article-template-box-control__header[\s\S]*?article-template-box-control__unit-select[\s\S]*?article-template-box-control__inputs/);
	assert.match(partial, /article-template-box-control__link/);
	assert.doesNotMatch(partial, /article-template-device-tabs--compact/);
	assert.match(partial, /optionsDevice/);
	assert.doesNotMatch(partial, /article-template-box-control__unit[^-]/);
	assert.match(css, /article-template-options-panel \.article-template-box-control__header[\s\S]*?display:\s*flex/);
	assert.match(css, /article-template-options-panel \.article-template-box-control__unit-select[\s\S]*?min-width:/);
});

test('template options switches match the approved inspector 44 by 24 geometry', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /--article-template-switch-width:\s*2\.75rem/);
	assert.match(css, /--article-template-switch-height:\s*1\.5rem/);
	assert.match(css, /article-template-options-panel \.form-switch \.form-check-input[\s\S]*?width:\s*var\(--article-template-switch-width\)/);
	assert.match(css, /article-template-options-panel \.form-switch \.form-check-input[\s\S]*?height:\s*var\(--article-template-switch-height\)/);
	assert.match(css, /article-template-options-panel \.form-switch \.form-check-input[\s\S]*?min-width:\s*var\(--article-template-switch-width\)/);
	assert.match(css, /article-template-options-panel \.form-switch \.form-check-input[\s\S]*?background-size:\s*1\.25rem 1\.25rem/);
});

test('template options uses a two-step compact 36px form scale', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const mockup = readFileSync(path.join(root, 'project-artifacts/mockups/template-options-20260905/forms-v3/index.html'), 'utf8');

	assert.match(css, /--article-template-control-height:\s*2\.25rem/);
	assert.match(css, /--article-template-action-height:\s*2\.125rem/);
	assert.match(css, /--article-template-unit-width:\s*4\.75rem/);
	assert.match(css, /article-template-options-panel \.article-template-option-row > \.form-switch[\s\S]*?min-height:\s*var\(--article-template-control-height\)/);
	assert.match(css, /article-template-options-panel \.article-template-position \.btn[\s\S]*?min-height:\s*var\(--article-template-action-height\)/);
	assert.match(css, /article-template-options-panel \.article-template-unit-control[\s\S]*?var\(--article-template-unit-width\)/);
	assert.match(mockup, /--control-v3:\s*36px/);
	assert.match(mockup, /--action-v3:\s*34px/);
	assert.match(mockup, /--switch-w:\s*44px/);
	assert.match(mockup, /--switch-h:\s*24px/);
});

test('compact radius controls keep all four sides usable on mobile', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /article-template-radius-side:nth-child\(3\)[\s\S]*?grid-column:\s*1/);
	assert.match(css, /article-template-radius-side:nth-child\(4\)[\s\S]*?grid-column:\s*2/);
	assert.match(css, /article-template-radius-link-cell[\s\S]*?grid-column:\s*3/);
});

test('toolbar and frame groups preserve full-width controls with readable rhythm', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');

	assert.match(css, /--article-template-setting-gap:\s*1rem/);
	assert.match(css, /article-template-options-panel \.article-template-options-fields[\s\S]*?gap:\s*var\(--article-template-setting-gap\)/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls\s*\{[\s\S]*?grid-template-columns:\s*minmax\(0, 1fr\)/);
	assert.match(css, /article-template-options-panel \.article-template-toolbar-option-controls\s*\{[\s\S]*?justify-content:\s*stretch/);
	assert.match(css, /article-template-options-panel \.article-template-frame-fields \.article-template-radius-unit\s*\{[\s\S]*?width:\s*var\(--article-template-unit-width\)/);
	assert.match(css, /article-template-options-panel \.article-template-frame-fields \.article-template-radius-unit\s*\{[\s\S]*?flex:\s*0 0 var\(--article-template-unit-width\)/);
	assert.match(css, /article-template-options-panel \.article-template-radius-label\s*\{[\s\S]*?white-space:\s*nowrap/);
});

test('spacing form groups use the wider rhythm and linked buttons share input borders', () => {
	const css = readFileSync(path.join(root, 'public/assets/css/article/article-template-manager-2026.css'), 'utf8');
	const mockup = readFileSync(path.join(root, 'project-artifacts/mockups/template-options-20260905/forms-v3/index.html'), 'utf8');

	assert.match(css, /--article-template-form-group-gap:\s*1\.25rem/);
	assert.match(css, /--article-template-form-field-gap:\s*\.875rem/);
	assert.match(css, /article-template-options-panel \.article-template-options-fields[\s\S]*?gap:\s*var\(--article-template-form-group-gap\)/);
	assert.match(css, /article-template-options-panel \.article-template-box-control[\s\S]*?gap:\s*var\(--article-template-form-field-gap\)/);
	assert.match(css, /article-template-options-panel \.article-template-radius-control[\s\S]*?gap:\s*var\(--article-template-form-field-gap\)/);
	assert.match(css, /article-template-options-panel \.article-template-radius-link,[\s\S]*?article-template-box-control__link[\s\S]*?border-color:\s*var\(--article-template-control-border\)/);
	assert.match(css, /--article-template-control-border:\s*var\(--bs-border-color/);
	assert.match(mockup, /--radius-unit-v4:\s*76px/);
	assert.match(mockup, /--setting-group-gap-v5:\s*20px/);
	assert.match(mockup, /\.form-group__devices\s*\{[^}]*display:\s*none/);
	assert.match(mockup, /\.box-link\.is-active\s*\{[^}]*border-color:\s*var\(--line-strong\)/);
});
