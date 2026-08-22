import { parse } from '@babel/parser';
import { readdir, readFile, writeFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';

const projectRoot = process.cwd();
const runtimePath = resolve(projectRoot, 'public/js/pagebuilder_elementor_v24/frontend-runtime.js');
const moduleRoot = resolve(projectRoot, 'resources/pagebuilder_elementor_v24/modules');
const original = await readFile(runtimePath, 'utf8');
const ast = parse(original, { sourceType: 'script' });
const iife = ast.program.body[0]?.expression?.callee;
if (!iife || !['FunctionExpression', 'ArrowFunctionExpression'].includes(iife.type)) {
  throw new Error('Expected the v2.4 frontend runtime to be an IIFE.');
}

const functions = new Map(
  iife.body.body
    .filter((node) => node.type === 'FunctionDeclaration' && node.id?.name)
    .map((node) => [node.id.name, { node, source: original.slice(node.start, node.end) }]),
);

function closure(initial, excluded = new Set()) {
  const selected = new Set(initial);
  let changed = true;
  while (changed) {
    changed = false;
    for (const name of [...selected]) {
      const entry = functions.get(name);
      if (!entry) throw new Error(`Missing runtime function: ${name}`);
      for (const candidate of functions.keys()) {
        if (selected.has(candidate) || excluded.has(candidate)) continue;
        if (new RegExp(`\\b${candidate}\\b`).test(entry.source)) {
          selected.add(candidate);
          changed = true;
        }
      }
    }
  }
  return [...selected]
    .map((name) => functions.get(name))
    .sort((left, right) => left.node.start - right.node.start)
    .map((entry) => entry.source);
}

const coreFunctionNames = new Set(['prefersReducedMotion', 'openMediaLightbox']);
const coreFunctions = closure(['prefersReducedMotion', 'bindAdvancedWidget', 'openMediaLightbox', 'init']);
const normalizedCoreFunctions = coreFunctions.map((source) => source.replace(
  "overlay.className = 'pb-image-lightbox pb-image-carousel-lightbox pb-pro-media-lightbox';",
  "overlay.className = ['pb-media-lightbox', String(settings.className || '').trim()].filter(Boolean).join(' ');",
));

const core = `(function () {
\t'use strict';

\tconst reducedMotionQuery = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
\tconst motionEntries = new Map();
\tlet motionFrame = 0;
\tlet motionListenersBound = false;
\tlet entranceObserver = null;

${normalizedCoreFunctions.join('\n\n')}

\twindow.PageBuilderElementorV24Runtime = Object.freeze({
\t\tinit,
\t\tbindAdvancedWidget,
\t\topenMediaLightbox,
\t\tprefersReducedMotion,
\t});

\tif (typeof document !== 'undefined') {
\t\tif (document.readyState === 'loading') {
\t\t\tdocument.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
\t\t} else {
\t\t\tinit(document);
\t\t}
\t}
})();
`;
parse(core, { sourceType: 'script' });

const runtimeSpecs = {
  image: { selector: '[data-basic-image]', initializer: 'bindBasicImage' },
  accordion: { selector: '[data-accordion-root]', initializer: 'bindAccordion' },
  basic_gallery: { selector: '[data-basic-gallery]', initializer: 'bindBasicGallery' },
  image_carousel: { selector: '[data-image-carousel]', initializer: 'bindImageCarousel' },
  tabs: { selector: '[data-tabs-widget]', initializer: 'initTabs' },
  animated_headline: { selector: '[data-pro-headline]', initializer: 'initProAnimatedHeadline' },
  carousel: { selector: '[data-pro-carousel].pb-pro-carousel:not(.pb-pro-reviews):not(.pb-pro-testimonial-carousel):not(.pb-pro-media-carousel)', initializer: 'initProCarousel' },
  code_highlight: { selector: '[data-code-highlight]', initializer: 'initProCodeHighlight' },
  countdown: { selector: '[data-pro-countdown]', initializer: 'initProCountdown' },
  flip_box: { selector: '[data-pro-flip-box]', initializer: 'initProFlipBox' },
  form: { selector: '[data-pro-form]', initializer: 'initProForm' },
  hero_banner: { selector: '[data-hero-banner]', initializer: 'initHeroBanner' },
  hero_slider: { selector: '[data-hero-slider]', initializer: 'initHeroSlider' },
  hotspot: { selector: '[data-pro-hotspot]', initializer: 'initProHotspot' },
  media_carousel: { selector: '[data-pro-carousel].pb-pro-media-carousel', initializer: 'initProCarousel' },
  product_color_selector: { selector: '[data-product-color-selector]', initializer: 'initProductColorSelector' },
  progress_tracker: { selector: '[data-progress-tracker]', initializer: 'initProProgressTracker' },
  reviews: { selector: '[data-pro-carousel].pb-pro-reviews', initializer: 'initProCarousel' },
  share_buttons: { selector: '[data-share-buttons]', initializer: 'initProShareButtons' },
  slides: { selector: '[data-pro-slides]', initializer: 'initProSlides' },
  testimonial_carousel: { selector: '[data-pro-carousel].pb-pro-testimonial-carousel', initializer: 'initProCarousel' },
  video_playlist: { selector: '[data-video-playlist]', initializer: 'initProVideoPlaylist' },
};

async function manifests(directory = moduleRoot) {
  const result = [];
  for (const entry of await readdir(directory, { withFileTypes: true })) {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) result.push(...await manifests(path));
    else if (entry.name === 'module.json') result.push(path);
  }
  return result;
}

const manifestByType = new Map();
for (const path of await manifests()) {
  const manifest = JSON.parse(await readFile(path, 'utf8'));
  manifestByType.set(manifest.type, { manifest, directory: dirname(path) });
}

for (const [type, spec] of Object.entries(runtimeSpecs)) {
  const module = manifestByType.get(type);
  if (!module?.manifest?.assets?.runtime) throw new Error(`Runtime manifest missing for ${type}`);
  let ownedFunctions = closure([spec.initializer], coreFunctionNames).join('\n\n');
  ownedFunctions = ownedFunctions.replace(
    "openMediaLightbox(source, 'image', alt);",
    "openMediaLightbox(source, 'image', alt, { className: 'pb-image-lightbox pb-image-carousel-lightbox' });",
  );
  const source = `(function (core) {
\t'use strict';
\tif (!core) return;

\tconst boundRoots = new WeakSet();
\tconst boundCarouselRoots = new WeakSet();
\tconst boundBasicGalleryRoots = new WeakSet();
\tconst boundBasicImageRoots = new WeakSet();
\tconst boundTabsRoots = new WeakSet();
\tconst boundProductColorSelectorRoots = new WeakSet();
\tconst heroSliderScriptPromises = new Map();
\tconst transitionState = new WeakMap();
\tconst prefersReducedMotion = () => core.prefersReducedMotion();
\tconst openMediaLightbox = (mediaSource, mediaType, alt, settings = {}) => core.openMediaLightbox(
\t\tmediaSource,
\t\tmediaType,
\t\talt,
\t\t{ className: 'pb-pro-media-lightbox', ...settings },
\t);

${ownedFunctions}

\tfunction init(scope) {
\t\tconst root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
\t\troot.querySelectorAll(${JSON.stringify(spec.selector)}).forEach(${spec.initializer});
\t}

\tconst runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
\truntimes[${JSON.stringify(type)}] = Object.freeze({ init, ${spec.initializer} });

\tif (typeof document !== 'undefined') {
\t\tif (document.readyState === 'loading') {
\t\t\tdocument.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
\t\t} else {
\t\t\tinit(document);
\t\t}
\t}
})(window.PageBuilderElementorV24Runtime);
`;
  parse(source, { sourceType: 'script' });
  await writeFile(join(module.directory, module.manifest.assets.runtime), source, 'utf8');
}

await writeFile(runtimePath, core, 'utf8');
console.log(`Extracted ${Object.keys(runtimeSpecs).length} module runtimes; core now owns only motion and generic media services.`);
