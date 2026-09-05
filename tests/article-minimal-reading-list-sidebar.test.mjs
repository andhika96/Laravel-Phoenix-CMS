import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const read = (relativePath) => readFileSync(path.join(root, relativePath), 'utf8');

test('Minimal Reading List adds the blog-list sidebar without losing Vue and pagination seams', () => {
	const template = read('resources/views/article/templates/archive/minimal-reading-list.blade.php');

	assert.match(template, /article-reading-list-layout/);
	assert.match(template, /article-reading-list__sidebar/);
	assert.match(template, /article-reading-list__categories/);
	assert.match(template, /article-reading-list__popular/);
	assert.match(template, /popularArticles/);
	assert.match(template, /data-article-vue-list-content/);
	assert.match(template, /data-article-vue-control-slot/);
	assert.match(template, /article\.templates\.partials\.pagination/);
});

test('Public archive SSR and Vue list responses receive sidebar articles', () => {
	const controller = read('app/Http/Controllers/Web/Article/ArticleFrontendController.php');

	assert.match(controller, /'popularArticles'\s*=>\s*\$context\['popularArticles'\]/);
	assert.match(controller, /\$popularArticles\s*=\s*\$this->publicArticleQuery\s*->eligible\(\)[\s\S]*?->limit\(4\)[\s\S]*?->get\(\)/);
});

test('Manage Article template preview passes fixture articles to the Minimal Reading List sidebar', () => {
	const controller = read('app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php');
	const preview = read('resources/views/manage_article/templates/preview.blade.php');

	assert.match(controller, /'popularArticles'\s*=>\s*\$popularArticles/);
	assert.match(controller, /\$articleCategories\s*=\s*\$template\s*===\s*'minimal-reading-list'/);
	assert.match(controller, /'articleCategories'\s*=>\s*\$articleCategories/);
	assert.match(preview, /'popularArticles'\s*=>\s*\$popularArticles\s*\?\?\s*collect\(\)/);
});

test('Minimal Reading List uses a responsive two-column layout and clean scoped pagination', () => {
	const css = read('public/assets/css/article/article-frontend-2026.css');

	assert.match(css, /\.article-page--reading-list \.article-reading-list-layout\s*\{[\s\S]*?display:\s*grid/);
	assert.match(css, /\.article-page--reading-list \.article-reading-list-layout\s*\{[\s\S]*?grid-template-columns:\s*minmax\(0, 1fr\)\s+minmax\(17rem, 20rem\)/);
	assert.match(css, /\.article-page--reading-list \.article-reading-list__sidebar/);
	assert.match(css, /\.article-page--reading-list \.article-reading-list-layout--without-sidebar\s*\{[\s\S]*?grid-template-columns:\s*minmax\(0, 1fr\)/);
	assert.match(css, /\.article-page--reading-list \.article-reading-list__media:not\(\.article-thumbnail-frame--custom\)/);
	assert.match(css, /\.article-page--reading-list \.article-reading-list__media\.article-thumbnail-frame--custom[\s\S]*?box-shadow:\s*none/);
	assert.match(css, /\.article-page--reading-list \.article-pagination--with-frame\s*\{[\s\S]*?border:\s*0/);
	assert.match(css, /@media \(max-width: 991\.98px\)[\s\S]*?\.article-page--reading-list \.article-reading-list-layout[\s\S]*?grid-template-columns:\s*1fr/);
});

test('Minimal Reading List search toolbar uses a single proportional control row', () => {
	const css = read('public/assets/css/article/article-frontend-2026.css');

	assert.match(css, /\.article-page--reading-list \.article-template-toolbar\s*\{[\s\S]*?display:\s*flex/);
	assert.match(css, /\.article-page--reading-list \.article-template-toolbar__zone:has\(\.article-template-toolbar__control--search\)\s*\{[\s\S]*?flex:\s*1 1 auto/);
	assert.match(css, /\.article-page--reading-list \.article-template-toolbar__control--search\s*\{[\s\S]*?width:\s*min\(100%,\s*42rem\)/);
	assert.match(css, /\.article-page--reading-list \.article-template-toolbar__control--search input\s*\{[\s\S]*?min-height:\s*48px/);
	assert.match(css, /\.article-page--reading-list \.article-template-toolbar \.btn\s*\{[\s\S]*?min-height:\s*48px/);
	assert.match(css, /@media \(max-width: 991\.98px\)[\s\S]*?\.article-page--reading-list \.article-template-toolbar\s*\{[\s\S]*?flex-direction:\s*column/);
});

test('Minimal Reading List sidebar visibility is part of the normalized manager options contract', () => {
	const options = read('app/Support/Article/ArticleTemplateOptions.php');
	const managerView = read('resources/views/manage_article/templates/index.blade.php');
	const managerJs = read('public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js');

	assert.match(options, /\$result\['sidebar'\]\s*=\s*\$this->sidebar/);
	assert.match(options, /sidebar\.categories\.enabled/);
	assert.match(options, /sidebar\.popular\.enabled/);
	assert.match(managerView, /Reading list sidebar/);
	assert.match(managerView, /optionsModal\.value\.sidebar\.categories\.enabled/);
	assert.match(managerView, /optionsModal\.value\.sidebar\.popular\.enabled/);
	assert.match(managerJs, /activeTemplateKey\s*===\s*'minimal-reading-list'[\s\S]*?options\.sidebar\s*=\s*options\.sidebar\s*\|\|\s*\{\}/);
	assert.match(managerJs, /options\.sidebar\.categories\s*=\s*options\.sidebar\.categories\s*\|\|\s*\{\}/);
	assert.match(managerJs, /options\.sidebar\.popular\s*=\s*options\.sidebar\.popular\s*\|\|\s*\{\}/);
});

test('sidebar dependent position controls stay under their owning toggle', () => {
	const managerView = read('resources/views/manage_article/templates/index.blade.php');

	assert.match(managerView, /article-template-sidebar-options/);
	assert.match(managerView, /article-template-sidebar-option/);

	const categories = managerView.indexOf("{{ t('Categories') }}");
	const categoriesPosition = managerView.indexOf("{{ t('Categories position') }}");
	const popular = managerView.indexOf("{{ t('Popular Posts') }}");
	const popularPosition = managerView.indexOf("{{ t('Popular Posts position') }}");

	assert.ok(categories >= 0 && categoriesPosition > categories);
	assert.ok(popular >= 0 && popularPosition > popular);
});
