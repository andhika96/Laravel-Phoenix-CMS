import { readdir, readFile, writeFile } from 'node:fs/promises';
import { join } from 'node:path';

const root = process.cwd();
const testRoot = join(root, 'tests');
const files = (await readdir(testRoot, { withFileTypes: true }))
  .filter((entry) => entry.isFile() && entry.name.includes('v24') && entry.name.endsWith('.mjs'))
  .map((entry) => join(testRoot, entry.name));

const direct = new Map([
  ['resources/views/pagebuilder_elementor_v24/partials/render_feature_showcase.blade.php', 'resources/pagebuilder_elementor_v24/modules/widgets/general/feature-showcase/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/partials/render_icon_list.blade.php', 'resources/pagebuilder_elementor_v24/modules/widgets/general/icon-list/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/widgets/layout/container.blade.php', 'resources/pagebuilder_elementor_v24/modules/layout/container/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/widgets/layout/grid.blade.php', 'resources/pagebuilder_elementor_v24/modules/layout/grid/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/widgets/basic/google-maps.blade.php', 'resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/widgets/general/tabs.blade.php', 'resources/pagebuilder_elementor_v24/modules/widgets/general/tabs/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/widgets/pro/hero-banner.blade.php', 'resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-banner/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/widgets/pro/hero-slider.blade.php', 'resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-slider/frontend.blade.php'],
  ['resources/views/pagebuilder_elementor_v24/widgets/pro/product-color-selector.blade.php', 'resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/frontend.blade.php'],
]);

let changed = 0;
for (const file of files) {
  const original = await readFile(file, 'utf8');
  let source = original.replace(
    /public\/js\/pagebuilder_elementor_v24\/widgets\/(layout|basic|general|pro)\/([a-z0-9-]+)\//g,
    (_, category, slug) => category === 'layout'
      ? `resources/pagebuilder_elementor_v24/modules/layout/${slug}/`
      : `resources/pagebuilder_elementor_v24/modules/widgets/${category}/${slug}/`,
  );
  for (const [legacy, canonical] of direct) source = source.replaceAll(legacy, canonical);
  if (source === original) continue;
  await writeFile(file, source, 'utf8');
  changed++;
}

console.log(`Rewrote ${changed} v2.4 Node test files to canonical module paths.`);
