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

test('Archive thumbnails use a background-cover media surface without distortion', () => {
	const frontendCss = readFileSync(path.join(root, 'public/assets/css/article/article-frontend-2026.css'), 'utf8');

	assert.match(frontendCss, /\.article-background-media/);
	assert.match(frontendCss, /background-size: cover/);
	assert.match(frontendCss, /background-position: center/);
	assert.match(frontendCss, /background-repeat: no-repeat/);

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
