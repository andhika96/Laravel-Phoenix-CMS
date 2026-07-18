import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const sourcePath = path.join(
	process.cwd(),
	'public/assets/js/vue3/manage_user/vueV3-manage-user-2026.js',
);
const source = readFileSync(sourcePath, 'utf8');

function loadManageUserOptions(documentStub)
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
		`${source}\n;globalThis.__manageUserOptions = ListUserVue3;`,
		sandbox,
		{ filename: sourcePath },
	);

	return sandbox.__manageUserOptions;
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

test('Manage User usable width excludes the browser scrollbar gutter', () =>
{
	const wrapper = { clientWidth: 1753, offsetWidth: 1768 };
	const options = loadManageUserOptions({
		getElementById(id)
		{
			return id === 'ph-user-table-wrapper' ? wrapper : null;
		},
	});

	assert.equal(options.methods.getTableWidth(), 1753);
});

test('Manage User column measurement preserves fractional layout widths', async () =>
{
	const headers = [
		{ offsetWidth: 95, getBoundingClientRect: () => ({ width: 95.328125 }) },
		{ offsetWidth: 250, getBoundingClientRect: () => ({ width: 250.265625 }) },
	];
	const options = loadManageUserOptions({
		querySelector(selector)
		{
			if (selector !== '#ph-user-table-wrapper table') return null;

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

test('Manage User ignores a one pixel rounding difference', () =>
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
	const options = loadManageUserOptions({
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

test('Manage User still hides a column for a material width shortage', () =>
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
	const options = loadManageUserOptions({
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
