import { readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

const featureRoot = resolve('tests/Feature');
const files = readdirSync(featureRoot)
  .filter((name) => /^PageBuilderElementorV24.*\.php$/.test(name))
  .map((name) => resolve(featureRoot, name));

const directViews = new Map([
  ['PageBuilderElementorV24FormRowGridRenderTest.php', [
    ['pagebuilder_elementor_v24.partials.render_pro_widget', 'form'],
  ]],
  ['PageBuilderElementorV24FormSubmissionTest.php', [
    ['pagebuilder_elementor_v24.partials.render_pro_widget', 'form'],
  ]],
  ['PageBuilderElementorV24ImageWidgetParityTest.php', [
    ['pagebuilder_elementor_v24.widgets.basic.image', 'image'],
  ]],
  ['PageBuilderElementorV24ProductColorSelectorWidgetTest.php', [
    ['pagebuilder_elementor_v24.widgets.pro.product-color-selector', 'product_color_selector'],
  ]],
]);

let changed = 0;
for (const file of files) {
  let source = readFileSync(file, 'utf8');
  const original = source;
  source = source.replace(
    /config\('pagebuilder_elementor_v24_widgets\.([a-z0-9_]+)'\)/g,
    (_, type) => `$this->pageBuilderV24Module('${type}')`,
  );
  source = source.replace(
    /view\(\$module\['view'\],/g,
    '$this->pageBuilderV24ModuleView($module,',
  );
  source = source.replace(
    /public_path\(\$module\['definition'\] \?\? 'missing'\)/g,
    "$module['assets']['definition'] ?? 'missing'",
  );

  for (const [legacyView, type] of directViews.get(file.split(/[\\/]/).at(-1)) || []) {
    source = source.replaceAll(
      `view('${legacyView}',`,
      `$this->pageBuilderV24ModuleViewByType('${type}',`,
    );
  }

  if (source !== original) {
    if (!source.includes('use Tests\\Concerns\\InteractsWithPageBuilderElementorV24Modules;')) {
      source = source.replace(
        'use Tests\\TestCase;',
        'use Tests\\Concerns\\InteractsWithPageBuilderElementorV24Modules;\nuse Tests\\TestCase;',
      );
    }
    if (!source.includes('use InteractsWithPageBuilderElementorV24Modules;')) {
      source = source.replace(
        /(class\s+PageBuilderElementorV24\w+\s+extends\s+TestCase\s*\{)/,
        '$1\n    use InteractsWithPageBuilderElementorV24Modules;',
      );
    }
    writeFileSync(file, source);
    changed += 1;
  }
}

console.log(`Migrated ${changed} Page Builder v2.4 PHP test files.`);
