import { readdir, readFile, writeFile } from 'node:fs/promises';
import { join, resolve } from 'node:path';

const root = resolve(process.cwd(), 'resources/pagebuilder_elementor_v24/modules');
const additions = {
  container: ['structure-container'],
  container_fluid: ['structure-container'],
  grid: ['structure-grid'],
  row_grid: ['structure-grid'],
  tabs: ['nested-tabs'],
  accordion: ['nested-accordion'],
  form: ['form-fields', 'draft-form', 'pro-icon-targets'],
  text_editor: ['rich-text-modal'],
  heading: ['advanced-normalization', 'shrinkable-inline'],
  button: ['shrinkable-inline', 'icon-source', 'primary-icon'],
  icon: ['icon-source', 'primary-icon', 'icon-link-options'],
  icon_box: ['icon-source', 'primary-icon'],
  icon_list: ['icon-source', 'icon-list-items'],
  image_carousel: ['icon-source', 'carousel-arrow-icons'],
  social_icons: ['icon-source', 'social-icon-items'],
  alert: ['icon-source', 'dismiss-icon'],
  rating: ['icon-source', 'rating-icon', 'primary-icon'],
  hero_slider: ['icon-source', 'carousel-arrow-icons'],
  media_carousel: ['icon-source', 'carousel-arrow-icons'],
  testimonial_carousel: ['icon-source', 'carousel-arrow-icons'],
  price_table: ['icon-source', 'pro-icon-targets'],
  reviews: ['icon-source', 'pro-icon-targets'],
  flip_box: ['icon-source', 'pro-icon-targets'],
};

async function manifestFiles(directory) {
  const result = [];
  for (const entry of await readdir(directory, { withFileTypes: true })) {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) result.push(...await manifestFiles(path));
    else if (entry.name === 'module.json') result.push(path);
  }
  return result;
}

let changed = 0;
for (const path of await manifestFiles(root)) {
  const manifest = JSON.parse(await readFile(path, 'utf8'));
  const next = [...new Set([...(manifest.capabilities || []), ...(additions[manifest.type] || [])])];
  if (JSON.stringify(next) === JSON.stringify(manifest.capabilities || [])) continue;
  manifest.capabilities = next;
  await writeFile(path, `${JSON.stringify(manifest, null, 2)}\n`, 'utf8');
  changed++;
}

console.log(`Added editor capabilities to ${changed} module manifests.`);
