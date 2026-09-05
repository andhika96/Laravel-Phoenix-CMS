import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(process.cwd(), 'public/assets/js/vue3/article/vueV3-article-frontend-2026.js');
const source = readFileSync(sourcePath, 'utf8');

test('Vue pagination exposes aria-current on the active page after hydration', () => {
	const attributes = [];
	const root = {
		dataset: {},
		querySelectorAll(selector) {
			assert.equal(selector, '[data-article-vue-control-slot] .page-item.active .page-link');

			return [{
				setAttribute(name, value) {
					attributes.push([name, value]);
				},
			}];
		},
	};
	const sandbox = {
		Vue: { createApp(options) { return { mount() { return options; } }; } },
		document: { getElementById() { return root; } },
		window: {},
		URLSearchParams,
	};

	vm.createContext(sandbox);
	vm.runInContext(`${source}\n;globalThis.__options = ArticleFrontendOptions;`, sandbox, { filename: sourcePath });

	assert.equal(typeof sandbox.__options.methods.syncPaginationAccessibility, 'function');
	sandbox.__options.methods.syncPaginationAccessibility();
	assert.deepEqual(attributes, [['aria-current', 'page']]);

	let updatedCalls = 0;
	assert.equal(typeof sandbox.__options.updated, 'function');
	sandbox.__options.updated.call({ syncPaginationAccessibility() { updatedCalls += 1; } });
	assert.equal(updatedCalls, 1);
});
