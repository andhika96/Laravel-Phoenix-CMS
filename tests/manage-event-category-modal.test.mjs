import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(process.cwd(), 'public/assets/js/vue3/manage_event/vueV3-manage-event-2026.js');
const source = readFileSync(sourcePath, 'utf8');

function loadOptions() {
	const sandbox = {
		Vue: {
			createApp(options) {
				return { mount() { return options; } };
			},
		},
		VuejsPaginateNext: {},
		document: { getElementById() { return null; } },
		window: {},
	};

	vm.createContext(sandbox);
	vm.runInContext(`${source}\n;globalThis.__options = ManageEventVue3;`, sandbox, { filename: sourcePath });

	return sandbox.__options;
}

test('resetting the Event category form removes stale selection and keeps automatic slug input-free', () => {
	const options = loadOptions();
	const state = {
		categoryForm: { id: 99, category_name: 'Old category', category_code: 'old-category', category_status: 'hide' },
		categoryModalMessage: 'Previous request failed',
	};

	options.methods.resetCategoryForm.call(state);

	assert.equal(state.categoryForm.id, '');
	assert.equal(state.categoryForm.category_name, '');
	assert.equal(state.categoryForm.category_code, '');
	assert.equal(state.categoryForm.category_status, 'active');
	assert.equal(state.categoryModalMessage, '');
});

test('Event category modal uses the Article-style list/create/edit/delete hierarchy', () => {
	const viewSource = readFileSync(path.join(process.cwd(), 'resources/views/manage_event/manage_event.blade.php'), 'utf8');

	assert.match(viewSource, /eventCategoryListModal/);
	assert.match(viewSource, /eventCategoryCreateModal/);
	assert.match(viewSource, /eventCategoryUpdateModal/);
	assert.match(viewSource, /eventCategoryDeleteModal/);
	assert.match(viewSource, /You can manage your categories here/);
	assert.match(viewSource, /Add New Category/);
	assert.doesNotMatch(viewSource, /id="eventCategoryModal"/);
});
