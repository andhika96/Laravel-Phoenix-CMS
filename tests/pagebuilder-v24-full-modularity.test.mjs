import assert from 'node:assert/strict';
import { readdirSync, readFileSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const moduleRoot = resolve(root, 'resources/pagebuilder_elementor_v24/modules');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v24/app.js'), 'utf8');
const coreRuntime = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v24/frontend-runtime.js'), 'utf8');
const editorCss = readFileSync(resolve(root, 'public/assets/css/pagebuilder_elementor_v24.css'), 'utf8');
const frontendCss = readFileSync(resolve(root, 'public/assets/css/frontend_elementor_v24.css'), 'utf8');
const shell = readFileSync(resolve(root, 'resources/views/pagebuilder_elementor_v24/editor_shell.blade.php'), 'utf8');
const renderNode = readFileSync(resolve(root, 'resources/views/pagebuilder_elementor_v24/partials/render_node.blade.php'), 'utf8');

function moduleManifests(directory = moduleRoot) {
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) return moduleManifests(path);
    if (entry.name !== 'module.json') return [];
    return [{ path, directory, manifest: JSON.parse(readFileSync(path, 'utf8')) }];
  });
}

const modules = moduleManifests();

test('all module definitions rely on manifest metadata and no legacy host runtime', () => {
  assert.equal(modules.length, 49);
  for (const module of modules) {
    const definition = readFileSync(join(module.directory, module.manifest.assets.definition), 'utf8');
    assert.doesNotMatch(definition, /\/js\/pagebuilder_elementor_v24\/widgets\//, module.manifest.type);
    assert.doesNotMatch(definition, /PageBuilderElementorV24ComplexWidgetRuntime/, module.manifest.type);
  }
  assert.doesNotMatch(app, /PageBuilderElementorV24ComplexWidgetRuntime/);
  assert.doesNotMatch(app, /\b(?:BASE_NODE_LABELS|NODE_LABEL_ICONS)\b/);
  const hostBranches = app.replace(/typeof\s+node\.type\s*===\s*['"]string['"]/g, '');
  assert.doesNotMatch(hostBranches, /(?:node|c)\.type\s*(?:===|!==)\s*['"][a-z][a-z0-9_]*['"]/);
  assert.doesNotMatch(app, /widgetRegistry\?*\.get\(['"][a-z][a-z0-9_]*['"]\)/);
  assert.doesNotMatch(renderNode, /pagebuilder_elementor_v24\.(?:widgets|partials\.render_(?!node))/);
  assert.doesNotMatch(renderNode, /match\s*\(\s*\$type\s*\)|in_array\(\$type,\s*\[/);
});

test('optional module runtimes own their initializers instead of delegating to core type functions', () => {
  const runtimeModules = modules.filter((module) => module.manifest.assets.runtime);
  assert.ok(runtimeModules.length > 0);

  for (const module of runtimeModules) {
    const runtime = readFileSync(join(module.directory, module.manifest.assets.runtime), 'utf8');
    assert.doesNotMatch(
      runtime,
      /runtime\.(?:bindAccordion|bindImageCarousel|bindBasicGallery|bindBasicImage|initTabs|initPro|initHero|initProductColorSelector)/,
      module.manifest.type,
    );
  }

  assert.doesNotMatch(coreRuntime, /\[data-(?:accordion|tabs|image-carousel|basic-gallery|basic-image|pro-|hero-|product-color-selector)/);
  assert.doesNotMatch(coreRuntime, /\b(?:bindAccordion|bindImageCarousel|bindBasicGallery|bindBasicImage|initTabs|initPro|initHero|initProductColorSelector)\b/);
});

test('module-specific selectors live in module styles rather than global stylesheets', () => {
  const globalCss = `${editorCss}\n${frontendCss}`;
  const moduleSelector = /(?:\.el-widget-accordion|\.pb-tabs(?:__|\b)|\.pb-basic-(?:image|gallery)|\.pb-image-carousel|\.pb-feature-showcase|\.pb-icon-list|\.pb-image-box|\.pb-icon-box|\.pb-pro-(?:form|slides|carousel|headline|hotspot|price|cta|countdown|reviews|testimonial|media|flip|code|blockquote|share|progress|video)|\.pb-hero-(?:banner|slider)|\.pb-product-color-selector)/;
  assert.doesNotMatch(globalCss, moduleSelector);
  assert.ok(modules.some((module) => module.manifest.assets.styles), 'type-specific CSS should be owned by module manifests');
});

test('editor shell inlines optional styles and runtimes from active module folders', () => {
  assert.match(shell, /\$pbElementorModule\['assets'\]\['styles'\]/);
  assert.match(shell, /\$pbElementorModule\['assets'\]\['runtime'\]/);
  assert.match(shell, /data-pb-module-definition/);
  assert.match(shell, /file_get_contents\(\$pbElementorModule\['assets'\]\['definition'\]\)/);
});
