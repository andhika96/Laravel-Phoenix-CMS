import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const root = process.cwd();
const sourcePath = path.join(root, 'public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js');
const source = readFileSync(sourcePath, 'utf8');

function loadOptions() {
	const sandbox = {
		Vue: { createApp(options) { return { mount() { return options; } }; } },
		document: { getElementById() { return { dataset: {} }; } },
		window: { setTimeout(callback) { callback(); return 1; }, clearTimeout() {} },
		URLSearchParams,
		console,
	};

	vm.createContext(sandbox);
	vm.runInContext(`${source}\n;globalThis.__options = ManageArticleTemplateVue3;`, sandbox, { filename: sourcePath });

	return sandbox.__options;
}

test('manager only materializes Minimal Reading List options', () => {
	const options = loadOptions();

	const minimalState = { activeTemplateKey: 'minimal-reading-list' };
	const minimal = options.methods.prepareTemplateOptions.call(minimalState, {}, 'archive');
	assert.deepEqual(JSON.parse(JSON.stringify(minimal.post_list)), { item_gap: '0.75rem' });
	assert.deepEqual(JSON.parse(JSON.stringify(minimal.sidebar)), {
		enabled: true,
		categories: { enabled: true, position: 'static' },
		popular: { enabled: true, position: 'static' },
	});

	const balancedState = { activeTemplateKey: 'balanced-card-grid' };
	const balanced = options.methods.prepareTemplateOptions.call(balancedState, {}, 'archive');
	assert.equal(Object.prototype.hasOwnProperty.call(balanced, 'sidebar'), false);
});
