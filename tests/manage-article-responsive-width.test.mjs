import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(
	process.cwd(),
	'public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js',
);
const source = readFileSync(sourcePath, 'utf8');

function loadManageArticleOptions(documentStub)
{
	const sandbox = {
		_: {
			debounce(callback)
			{
				return callback;
			},
		},
		VuejsPaginateNext: {},
		console,
		createApp(options)
		{
			return {
				mount()
				{
					return options;
				},
			};
		},
		document: documentStub,
		requestAnimationFrame(callback)
		{
			callback();
		},
		setTimeout,
		clearTimeout,
	};

	vm.createContext(sandbox);
	vm.runInContext(
		`${source}\n;globalThis.__manageArticleOptions = ManageArticleVue3;`,
		sandbox,
		{ filename: sourcePath },
	);

	return sandbox.__manageArticleOptions;
}

function makeHeader(index, priority)
{
	return {
		getAttribute(name)
		{
			if (name === 'data-col-idx') return String(index);
			if (name === 'data-col-priority') return String(priority);
			return null;
		},
	};
}

test('usable table width excludes the browser scrollbar gutter', () =>
{
	const wrapper = { clientWidth: 1753, offsetWidth: 1768 };
	const options = loadManageArticleOptions({
		getElementById(id)
		{
			return id === 'ph-article-table-wrapper' ? wrapper : null;
		},
	});

	assert.equal(options.methods.getTableWidth(), 1753);
});

test('column measurement preserves fractional layout widths', async () =>
{
	const headers = [
		{ offsetWidth: 95, getBoundingClientRect: () => ({ width: 95.328125 }) },
		{ offsetWidth: 250, getBoundingClientRect: () => ({ width: 250.265625 }) },
	];
	const options = loadManageArticleOptions({
		querySelector(selector)
		{
			if (selector !== '#ph-article-table-wrapper table') return null;

			return {
				querySelectorAll: () => headers,
			};
		},
	});
	const state = { responsiveHiddenCols: [] };

	await new Promise((resolve) =>
	{
		options.methods.measureColWidths.call(state, resolve);
	});

	assert.deepEqual(
		[...Object.values(state._colNaturalWidths)],
		[95.328125, 250.265625],
	);
});

test('a one pixel rounding difference does not hide Options', () =>
{
	const headers = [
		makeHeader(0, 0),
		makeHeader(1, 'all'),
		makeHeader(2, 2),
		makeHeader(3, 3),
		makeHeader(4, 4),
		makeHeader(5, 5),
		makeHeader(6, 6),
	];
	const options = loadManageArticleOptions({
		querySelectorAll: () => headers,
	});
	const state = {
		_colNaturalWidths: {
			0: 106,
			1: 279,
			2: 331,
			3: 235,
			4: 176,
			5: 343,
			6: 284,
		},
		getTableWidth: () => 1753,
		responsiveExpandedRows: { 4: true },
		responsiveHiddenCols: [],
	};

	options.methods.recalcResponsive.call(state);

	assert.deepEqual([...state.responsiveHiddenCols], []);
});

test('a material width shortage still hides Options', () =>
{
	const headers = [
		makeHeader(0, 0),
		makeHeader(1, 'all'),
		makeHeader(2, 2),
		makeHeader(3, 3),
		makeHeader(4, 4),
		makeHeader(5, 5),
		makeHeader(6, 6),
	];
	const options = loadManageArticleOptions({
		querySelectorAll: () => headers,
	});
	const state = {
		_colNaturalWidths: {
			0: 106,
			1: 279,
			2: 331,
			3: 235,
			4: 176,
			5: 343,
			6: 284,
		},
		getTableWidth: () => 1700,
		responsiveExpandedRows: {},
		responsiveHiddenCols: [],
	};

	options.methods.recalcResponsive.call(state);

	assert.deepEqual([...state.responsiveHiddenCols], [6]);
});
