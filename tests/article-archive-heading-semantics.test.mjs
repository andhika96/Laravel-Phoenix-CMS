import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const header = readFileSync(
	path.join(process.cwd(), 'resources/views/article/templates/partials/archive-header.blade.php'),
	'utf8',
);

test('archive header preserves one accessible H1 when the visual title is disabled', () => {
	assert.match(header, /!data_get\(\$copy, 'title\.enabled'\) \|\| !data_get\(\$copy, 'title\.text'\)/);
	assert.match(header, /<h1 class="visually-hidden">\{\{ t\('Articles'\) \}\}<\/h1>/);
});
