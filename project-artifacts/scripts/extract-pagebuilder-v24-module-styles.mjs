import postcss from 'postcss';
import { readdir, readFile, writeFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';

const projectRoot = process.cwd();
const moduleRoot = resolve(projectRoot, 'resources/pagebuilder_elementor_v24/modules');
const cssFiles = [
  resolve(projectRoot, 'public/assets/css/pagebuilder_elementor_v24.css'),
  resolve(projectRoot, 'public/assets/css/frontend_elementor_v24.css'),
];

async function manifests(directory = moduleRoot) {
  const result = [];
  for (const entry of await readdir(directory, { withFileTypes: true })) {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) result.push(...await manifests(path));
    else if (entry.name === 'module.json') result.push(path);
  }
  return result;
}

const moduleByType = new Map();
for (const path of await manifests()) {
  const manifest = JSON.parse(await readFile(path, 'utf8'));
  moduleByType.set(manifest.type, { path, directory: dirname(path), manifest });
}

const proTypes = [...moduleByType.values()]
  .filter(({ manifest }) => manifest.category === 'pro')
  .map(({ manifest }) => manifest.type);

const matchers = [
  { types: ['container', 'container_fluid'], pattern: /\.(?:el-layout-container(?:-fluid)?|pb-container(?:-|\b)|pb-node-container(?:_fluid)?\b|pb-dropzone-container-children|pb-is-resizing-containers|pb-has-background-media|pb-bg-media-|pb-background-video-mobile-disabled)/ },
  { types: ['grid', 'row_grid'], pattern: /\.(?:el-layout-(?:grid|row-grid)|el-grid-|pb-grid-|pb-node-(?:grid|row_grid)\b|element-grid\b|grid-off\b)/ },
  { types: ['heading'], pattern: /\.(?:el-widget-heading\b|pb-heading-|pb-node-heading\b|has-single-heading\b)/ },
  { types: ['video'], pattern: /\.(?:el-widget-video\b|el-video-|pb-video-|pb-node-video\b)/ },
  { types: ['google_maps'], pattern: /(?:google-maps|google_maps|data-google-maps)/ },
  { types: ['text_editor'], pattern: /\.(?:el-widget-text-editor\b|pb-widget-settings--text-editor\b|pb-basic-text-style-settings\b|pb-node-text_editor\b)/ },
  { types: ['image'], pattern: /\.(?:el-widget-image\b|pb-basic-image\b|pb-node-image\b|pb-widget-settings--image\b)/ },
  { types: ['button'], pattern: /\.(?:el-widget-button\b|pb-basic-button|pb-widget-settings--button\b|pb-node-button\b)/ },
  { types: ['divider'], pattern: /\.(?:el-widget-divider\b|pb-basic-divider|pb-widget-settings--divider\b|pb-node-divider\b)/ },
  { types: ['spacer'], pattern: /\.(?:el-widget-spacer\b|pb-widget-settings--spacer\b|pb-node-spacer\b)/ },
  { types: ['icon'], pattern: /\.(?:el-widget-icon(?:-link)?\b|pb-basic-icon|pb-widget-settings--icon\b|pb-node-icon\b)/ },
  { types: ['image_box'], pattern: /\.(?:el-widget-image-box\b|pb-image-box|pb-widget-settings--image-box\b|pb-node-image_box\b)/ },
  { types: ['icon_box'], pattern: /\.(?:el-widget-icon-box\b|pb-icon-box|pb-widget-settings--icon-box\b|pb-node-icon_box\b)/ },
  { types: ['image_carousel'], pattern: /\.(?:pb-image-carousel|pb-carousel-|pb-widget-settings--image-carousel\b|pb-node-image_carousel\b)/ },
  { types: ['image', 'image_carousel', 'basic_gallery'], pattern: /\.(?:pb-image-lightbox|pb-image-carousel-lightbox)\b/ },
  { types: ['basic_gallery'], pattern: /\.(?:pb-basic-gallery|pb-widget-settings--basic-gallery\b|pb-node-basic_gallery\b)/ },
  { types: ['feature_showcase'], pattern: /\.(?:pb-feature-showcase|pb-widget-settings--feature-showcase\b|pb-node-feature_showcase\b)/ },
  { types: ['icon_list'], pattern: /\.(?:pb-icon-list|pb-widget-settings--icon-list\b|pb-node-icon_list\b)/ },
  { types: ['tabs'], pattern: /\.(?:el-widget-tabs|el-tabs-nav|pb-tabs-|pb-preview-tabs\b|pb-dropzone-tab\b|pb-tabs-settings\b|pb-node-tabs\b)/ },
  { types: ['accordion'], pattern: /\.(?:el-widget-accordion|pb-accordion-|pb-preview-accordion\b|pb-dropzone-accordion\b|pb-accordion-settings\b|pb-node-accordion\b)/ },
  { types: ['counter'], pattern: /\.(?:el-widget-counter\b|pb-counter|pb-widget-settings--counter\b|pb-node-counter\b)/ },
  { types: ['progress_bar'], pattern: /\.(?:el-widget-progress-bar\b|pb-progress-bar|pb-widget-settings--progress-bar\b|pb-node-progress_bar\b)/ },
  { types: ['testimonial'], pattern: /\.(?:el-widget-testimonial\b|pb-testimonial|pb-widget-settings--testimonial\b|pb-node-testimonial\b)/ },
  { types: ['social_icons'], pattern: /\.(?:el-widget-social-icons\b|pb-social-|pb-widget-settings--social-icons\b|pb-node-social_icons\b)/ },
  { types: ['alert'], pattern: /\.(?:el-widget-alert\b|pb-alert|pb-widget-settings--alert\b|pb-node-alert\b)/ },
  { types: ['rating'], pattern: /\.(?:pb-rating|pb-widget-settings--rating\b|pb-node-rating\b)/ },
  { types: ['text_path'], pattern: /\.(?:pb-text-path|pb-widget-settings--text-path\b|pb-node-text_path\b)/ },
  { types: ['hero_banner'], pattern: /\.(?:pb-hero-banner|pb-node-hero_banner\b)/ },
  { types: ['hero_slider'], pattern: /\.(?:pb-hero-slider|pb-node-hero_slider\b)/ },
  { types: ['product_color_selector'], pattern: /\.(?:pb-product-color-selector|pb-node-product_color_selector\b)/ },
  { types: proTypes, pattern: /\.(?:pb-pro-(?:arrow|dots|button|icon-svg|media-lightbox)\b)/ },
];

const proAliases = {
  animated_headline: ['headline', 'animated-headline'],
  call_to_action: ['cta', 'call-to-action'],
  price_list: ['price-list', 'price'],
  price_table: ['price-table', 'price'],
  progress_tracker: ['progress-tracker', 'progress'],
  testimonial_carousel: ['testimonial-carousel', 'testimonial'],
  media_carousel: ['media-carousel', 'media'],
  video_playlist: ['video-playlist', 'video'],
  share_buttons: ['share-buttons', 'share'],
  code_highlight: ['code-highlight', 'code'],
  flip_box: ['flip-box', 'flip'],
};
for (const type of proTypes) {
  const aliases = new Set([type.replaceAll('_', '-'), ...(proAliases[type] || [])]);
  matchers.push({
    types: [type],
    pattern: new RegExp(`\\.pb-pro-(?:${[...aliases].join('|')})(?:__|--|\\b)`),
  });
}

function splitSelectors(selector) {
  const selectors = [];
  let start = 0;
  let depth = 0;
  let quote = '';
  for (let index = 0; index < selector.length; index++) {
    const character = selector[index];
    if (quote) {
      if (character === quote && selector[index - 1] !== '\\') quote = '';
      continue;
    }
    if (character === '"' || character === "'") quote = character;
    else if (character === '(' || character === '[') depth++;
    else if (character === ')' || character === ']') depth = Math.max(0, depth - 1);
    else if (character === ',' && depth === 0) {
      selectors.push(selector.slice(start, index).trim());
      start = index + 1;
    }
  }
  selectors.push(selector.slice(start).trim());
  return selectors.filter(Boolean);
}

function typesFor(selector) {
  const types = new Set();
  for (const matcher of matchers) {
    if (matcher.pattern.test(selector)) matcher.types.forEach((type) => types.add(type));
  }
  return types;
}

function appendWithParents(root, rule, selector) {
  let clone = rule.clone({ selector });
  let parent = rule.parent;
  while (parent && parent.type !== 'root') {
    const wrapper = parent.clone({ nodes: [] });
    wrapper.append(clone);
    clone = wrapper;
    parent = parent.parent;
  }
  root.append(clone);
}

const moduleRoots = new Map([...moduleByType.keys()].map((type) => [type, postcss.root()]));
let movedSelectors = 0;
for (const cssPath of cssFiles) {
  const root = postcss.parse(await readFile(cssPath, 'utf8'), { from: cssPath });
  root.walkRules((rule) => {
    const retained = [];
    for (const selector of splitSelectors(rule.selector)) {
      const types = typesFor(selector);
      if (!types.size) {
        retained.push(selector);
        continue;
      }
      for (const type of types) appendWithParents(moduleRoots.get(type), rule, selector);
      movedSelectors++;
    }
    if (retained.length) rule.selector = retained.join(',\n');
    else rule.remove();
  });
  await writeFile(cssPath, root.toString(), 'utf8');
}

let styleModules = 0;
for (const [type, module] of moduleByType) {
  const css = moduleRoots.get(type).toString().trim();
  if (!css) continue;
  await writeFile(join(module.directory, 'styles.css'), `${css}\n`, 'utf8');
  module.manifest.assets.styles = 'styles.css';
  await writeFile(module.path, `${JSON.stringify(module.manifest, null, 2)}\n`, 'utf8');
  styleModules++;
}

console.log(`Moved ${movedSelectors} selectors into ${styleModules} module stylesheets.`);
