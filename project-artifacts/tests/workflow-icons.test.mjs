import assert from 'node:assert/strict';
import test from 'node:test';
import {
  FONT_AWESOME_METADATA_URL,
  buildFontAwesomeIconLibrary,
  filterIconLibrary,
  loadFontAwesomeIconLibrary,
  parseIconClass,
} from '../../public/assets/js/vue3/workflow/workflow-icons.js';

const metadata = {
  star: { label: 'Star', search: { terms: ['favorite', 'rating'] }, styles: ['solid', 'regular'] },
  github: { label: 'GitHub', search: { terms: ['brand', 'repository'] }, styles: ['brands'] },
  'arrow-right': { label: 'Arrow Right', search: { terms: ['next'] }, styles: ['solid'] },
};

test('builds the five Font Awesome style prefixes from metadata without SVG output', () => {
  const icons = buildFontAwesomeIconLibrary({ ...metadata, sample: { styles: ['light', 'duotone'] } });
  assert.deepEqual(icons.filter((item) => item.name === 'sample').map((item) => item.className), ['fad fa-sample', 'fal fa-sample']);
  assert.equal(icons.some((item) => 'svg' in item || 'path' in item), false);
});

test('filters by style and name, label, or search terms and paginates', () => {
  const icons = buildFontAwesomeIconLibrary(metadata);
  assert.equal(filterIconLibrary(icons, { group: 'brands' }).items[0].className, 'fab fa-github');
  assert.equal(filterIconLibrary(icons, { query: 'favorite' }).items[0].className, 'far fa-star');
  const page = filterIconLibrary(icons, { limit: 1, offset: 1 });
  assert.equal(page.total, 4);
  assert.equal(page.items.length, 1);
});

test('parses supported icon classes and falls back to regular star metadata', () => {
  assert.deepEqual(parseIconClass('fab fa-github'), { style: 'brands', name: 'github' });
  assert.deepEqual(parseIconClass('invalid'), { style: 'regular', name: 'star' });
});

test('loads the local metadata URL lazily through the supplied fetcher', async () => {
  let requested;
  const icons = await loadFontAwesomeIconLibrary(async (url) => {
    requested = url;
    return { ok: true, async json() { return metadata; } };
  });
  assert.equal(requested, FONT_AWESOME_METADATA_URL);
  assert.equal(icons.length, 4);
});
