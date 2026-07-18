import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const bladePath = path.join(
	process.cwd(),
	'resources/views/awesome_admin/awesome_admin_smtp.blade.php',
);
const sourcePath = path.join(
	process.cwd(),
	'public/assets/js/vue3/manage_smtp/vueV3-manage-smtp-2026.js',
);
const blade = readFileSync(bladePath, 'utf8');
const source = readFileSync(sourcePath, 'utf8');

function loadManageSmtpOptions(documentStub)
{
	const sandbox = {
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
		`${source}\n;globalThis.__manageSmtpOptions = ListDataSMTPVue3;`,
		sandbox,
		{ filename: sourcePath },
	);

	return sandbox.__manageSmtpOptions;
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

test('SMTP table declares responsive priorities and an expandable service column', () =>
{
	assert.match(blade, /id="ph-smtp-table-wrapper"/);
	assert.match(blade, /data-col-idx="0" data-col-priority="1"[^>]*v-show="!isColHidden\(0\)"/);
	assert.match(blade, /data-col-idx="1" data-col-priority="all"/);
	assert.match(blade, /data-col-idx="9" data-col-priority="9"[^>]*v-show="!isColHidden\(9\)"/);
	assert.match(blade, /ph-dtr-title-expandable[\s\S]*?toggleExpandRow\(info\.id\)/);
});

test('SMTP child row preserves hidden values and Vue-bound actions', () =>
{
	assert.match(
		blade,
		/v-if="responsiveExpandedRows\[info\.id\] && responsiveHiddenCols\.length > 0" class="ph-dtr-child-row"/,
	);
	assert.match(blade, /v-if="isColHidden\(4\)"[\s\S]*?info\.smtp_password/);
	assert.match(
		blade,
		/v-if="isColHidden\(9\)"[\s\S]*?openModalEditSMTP\(\$event, info\.id\)[\s\S]*?openModalDeleteSMTP\(\$event, info\.id\)/,
	);
});

test('SMTP measures columns only after the table content is visible', () =>
{
	assert.match(
		source,
		/document\.querySelector\("\.ph-data-load-content"\)\.style\.display = 'block';[\s\S]*?this\.\$nextTick\(\(\) =>[\s\S]*?this\.setupResponsiveTable\(\);/,
	);
});

test('SMTP responsive measurement uses usable and fractional widths', async () =>
{
	const wrapper = { clientWidth: 1753, offsetWidth: 1768 };
	const headers = [
		{ offsetWidth: 95, getBoundingClientRect: () => ({ width: 95.328125 }) },
		{ offsetWidth: 250, getBoundingClientRect: () => ({ width: 250.265625 }) },
	];
	const options = loadManageSmtpOptions({
		getElementById(id)
		{
			return id === 'ph-smtp-table-wrapper' ? wrapper : null;
		},
		querySelector(selector)
		{
			if (selector !== '#ph-smtp-table-wrapper table') return null;

			return {
				querySelectorAll: () => headers,
			};
		},
	});
	const state = { responsiveHiddenCols: [] };

	assert.equal(options.methods.getTableWidth(), 1753);

	await new Promise((resolve) =>
	{
		options.methods.measureColWidths.call(state, resolve);
	});

	assert.deepEqual(
		[...Object.values(state._colNaturalWidths)],
		[95.328125, 250.265625],
	);
});

test('SMTP responsive calculation tolerates rounding but hides material overflow', () =>
{
	const headers = [
		makeHeader(0, 1),
		makeHeader(1, 'all'),
		makeHeader(2, 2),
		makeHeader(3, 3),
		makeHeader(4, 4),
		makeHeader(5, 5),
		makeHeader(6, 6),
		makeHeader(7, 7),
		makeHeader(8, 8),
		makeHeader(9, 9),
	];
	const options = loadManageSmtpOptions({
		querySelectorAll: () => headers,
	});
	const widths = {
		0: 80,
		1: 200,
		2: 100,
		3: 120,
		4: 120,
		5: 80,
		6: 80,
		7: 80,
		8: 80,
		9: 61,
	};
	const roundingState = {
		_colNaturalWidths: widths,
		getTableWidth: () => 1000,
		responsiveExpandedRows: { 4: true },
		responsiveHiddenCols: [],
	};

	options.methods.recalcResponsive.call(roundingState);
	assert.deepEqual([...roundingState.responsiveHiddenCols], []);

	const shortageState = {
		_colNaturalWidths: widths,
		getTableWidth: () => 950,
		responsiveExpandedRows: {},
		responsiveHiddenCols: [],
	};

	options.methods.recalcResponsive.call(shortageState);
	assert.deepEqual([...shortageState.responsiveHiddenCols], [9]);
});
