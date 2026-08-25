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
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-reading-list__item\s*\{[\s\S]*?padding: 1\.125rem 0/);
	assert.match(css, /@media \(max-width: 575\.98px\)\s*\{[\s\S]*?\.article-editorial-lead\s*\{[\s\S]*?gap: 1\.25rem/);
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
