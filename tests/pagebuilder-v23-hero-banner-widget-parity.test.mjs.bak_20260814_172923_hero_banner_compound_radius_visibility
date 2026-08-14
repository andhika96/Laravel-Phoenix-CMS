import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => fs.existsSync(path.join(root, relative)) ? fs.readFileSync(path.join(root, relative), 'utf8') : '';

const definition = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/definition.js');
const settings = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Settings.vue');
const canvas = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Canvas.vue');
const blade = read('resources/views/pagebuilder_elementor_v23/widgets/pro/hero-banner.blade.php');
const config = read('config/pagebuilder_elementor_v23_widgets.php');
const app = read('public/js/pagebuilder_elementor_v23/app.js');
const runtime = read('public/js/pagebuilder_elementor_v23/frontend-runtime.js');
const frontendCss = read('public/assets/css/frontend_elementor.css');
const editorCss = read('public/assets/css/pagebuilder_elementor_v23.css');

assert.ok(definition, 'Hero Banner definition must exist');
assert.match(definition, /type:\s*['"]hero_banner['"]/);
assert.match(definition, /label:\s*['"]Hero Banner['"]/);
assert.match(definition, /category:\s*['"]pro['"]/);
assert.match(definition, /\.slice\(0,\s*3\)/);
assert.match(definition, /contentOrder/);

assert.match(config, /'hero_banner'\s*=>/);
assert.match(config, /widgets\/pro\/hero-banner\/definition\.js/);
assert.match(config, /widgets\/pro\/hero-banner\/Canvas\.vue/);
assert.match(config, /widgets\/pro\/hero-banner\/Settings\.vue/);
assert.match(config, /widgets\.pro\.hero-banner/);
assert.match(app, /hero_banner:\s*'Hero Banner'/);
assert.match(app, /hero_banner:\s*'fas fa-image'/);
assert.match(app, /video_playlist',\s*'hero_banner'/);

for (const marker of ['Content Behavior', 'Content Order', 'Add Button', 'Action Type', 'Video Source', 'Image Source', 'Responsive Position', 'Button Group Layout', 'Responsive Media', "editor.linkControl", 'editor.chooseMedia', "editor.settingsTab==='style'", "editor.settingsTab==='advanced'"]) {
  assert.ok(settings.includes(marker), `Settings must include ${marker}`);
}
assert.match(settings, /buttons\.length\s*>=\s*3/);
assert.match(settings, /buttons\.length\s*<=\s*1/);
assert.match(settings, /class="pb-widget-settings pb-widget-settings--general-new pb-widget-settings--pro pb-hero-settings"/);
assert.ok((settings.match(/class="pb-seg-btn"/g) || []).length >= 6, 'Hero segmented controls must reuse the v2.3 button class');
assert.equal((settings.match(/<ToggleField/g) || []).length, 4, 'Hero boolean controls must reuse one compact toggle component');
assert.doesNotMatch(settings, /pb-switch-row/, 'Hero settings must not render unstyled native checkbox rows');
assert.match(settings, /:deep\(\.pb-hero-devices button\)/);
assert.match(settings, /:deep\(\.pb-hero-number\)/);
assert.match(settings, /:deep\(\.pb-hero-color\)/);
assert.match(settings, /\.pb-hero-mode \.pb-seg-btn\{gap:5px\}/);

assert.match(canvas, /data-hero-banner/);
assert.match(canvas, /contentOrder/);
assert.match(canvas, /positioningMode/);
assert.match(canvas, /PageBuilderElementorV23Runtime\?\.openMediaLightbox/);
assert.match(canvas, /responsiveValue\(/);

assert.match(blade, /data-hero-banner/);
assert.match(blade, /data-hero-media/);
assert.match(blade, /@media\(max-width:1024px\)/);
assert.match(blade, /@media\(max-width:767px\)/);
assert.match(frontendCss, /\.pb-hero-banner/);
assert.match(editorCss, /\.pb-node\.pb-node-hero_banner \[data-pb-interactive="true"\]\s*\{[^}]*pointer-events:\s*auto/s);

assert.match(runtime, /function initHeroBanner\(/);
assert.match(runtime, /\[data-hero-banner\]/);
assert.match(runtime, /\[data-hero-media\]/);
assert.ok(runtime.includes('www\\.dailymotion\\.com\\/embed\\/video'));
assert.match(runtime, /document\.createElement\(isNativeVideo \? 'video' : 'iframe'\)/);
assert.match(runtime, /openMediaLightbox/);
assert.match(runtime, /initHeroBanner/);
assert.match(runtime, /PageBuilderElementorV23Runtime\s*=\s*Object\.freeze\(\{[^}]*openMediaLightbox/s);

console.log('pagebuilder v2.3 hero banner widget parity test passed');
