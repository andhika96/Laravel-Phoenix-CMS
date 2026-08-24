import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const root = process.cwd();
const scriptPath = path.join(root, 'public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js');
const addViewPath = path.join(root, 'resources/views/manage_article/manage_article_add.blade.php');
const editViewPath = path.join(root, 'resources/views/manage_article/manage_article_edit.blade.php');
const thumbnailPartialPath = path.join(root, 'resources/views/manage_article/partials/thumbnail.blade.php');
const scriptSource = readFileSync(scriptPath, 'utf8');

function loadOptions() {
	const sandbox = {
		_: { debounce(callback) { return callback; } },
		VuejsPaginateNext: {},
		console,
		createApp(options) {
			return { mount() { return options; } };
		},
		document: { getElementById() { return null; } },
		requestAnimationFrame(callback) { callback(); },
		setTimeout,
		clearTimeout,
	};

	vm.createContext(sandbox);
	vm.runInContext(`${scriptSource}\n;globalThis.__options = ManageArticleVue3;`, sandbox, { filename: scriptPath });

	return sandbox.__options;
}

test('Article add and edit forms share the Event-style thumbnail source picker', () => {
	const addSource = readFileSync(addViewPath, 'utf8');
	const editSource = readFileSync(editViewPath, 'utf8');
	const partialSource = readFileSync(thumbnailPartialPath, 'utf8');

	assert.match(addSource, /@include\('manage_article\.partials\.thumbnail'\)/);
	assert.match(editSource, /@include\('manage_article\.partials\.thumbnail'\)/);
	assert.match(partialSource, /name="thumbnail_source"/);
	assert.match(partialSource, /name="thumbnail_ckfinder_url"/);
	assert.match(partialSource, /Upload file/);
	assert.match(partialSource, /CKFinder library/);
	assert.match(partialSource, /Browse CKFinder/);
	assert.match(partialSource, /btn btn-outline-danger/);
	assert.match(partialSource, /No thumbnail selected/);
	assert.match(scriptSource, /CKFinder\.modal/);
	assert.match(scriptSource, /resourceType: 'Articles'/);
	assert.doesNotMatch(scriptSource, /CKFinder\.popup/);
});

test('switching from selected Article CKFinder image back to upload clears stale selection', () => {
	const options = loadOptions();
	const state = {
		thumbnailSource: 'ckfinder',
		thumbnailCkfinderUrl: '/storage/ckfinder/articles/selected.jpg',
		thumbnailCkfinderLabel: 'Articles / selected.jpg',
		thumbnailOriginalPreview: '',
		thumbnailFileSelected: false,
		imageEncoded: '/storage/ckfinder/articles/selected.jpg',
		showButtonRemoveImage: true,
		articleThumbnailRemove: false,
		$refs: { thumbnailInput: { value: '' } },
	};

	options.methods.setArticleThumbnailSource.call(state, 'upload');

	assert.equal(state.thumbnailSource, 'upload');
	assert.equal(state.thumbnailCkfinderUrl, '');
	assert.equal(state.thumbnailCkfinderLabel, '');
	assert.equal(state.imageEncoded, '');
	assert.equal(state.showButtonRemoveImage, false);
});
