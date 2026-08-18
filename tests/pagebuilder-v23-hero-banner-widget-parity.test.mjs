import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => fs.existsSync(path.join(root, relative)) ? fs.readFileSync(path.join(root, relative), 'utf8') : '';

const definition = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/definition.js');
const settings = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Settings.vue');
const settingsMarkup = settings.split('<style scoped>')[0];
const canvas = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Canvas.vue');
const blade = read('resources/views/pagebuilder_elementor_v23/widgets/pro/hero-banner.blade.php');
const config = read('config/pagebuilder_elementor_v23_widgets.php');
const app = read('public/js/pagebuilder_elementor_v23/app.js');
const runtime = read('public/js/pagebuilder_elementor_v23/frontend-runtime.js');
const frontendCss = read('public/assets/css/frontend_elementor.css');
const frontendV23Css = read('public/assets/css/frontend_elementor_v23.css');
const editorCss = read('public/assets/css/pagebuilder_elementor_v23.css');
const prototypeCss = read('mockups/pagebuilder-v23-responsive-hero-prototype/src/styles.css');
const prototype = read('public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html');

assert.ok(definition, 'Hero Banner definition must exist');
assert.match(definition, /type:\s*['"]hero_banner['"]/);
assert.match(definition, /label:\s*['"]Hero Banner['"]/);
assert.match(definition, /category:\s*['"]pro['"]/);
assert.match(definition, /\.slice\(0,\s*3\)/);
assert.match(definition, /contentOrder/);

const registrations = [];
vm.runInNewContext(definition, {
    window: {
        PageBuilderElementorV23ComplexWidgetRuntime: { image_box: { defaults: () => ({}) } },
        PageBuilderElementorV23Widgets: { register(widget) { registrations.push(widget); } },
    },
});
assert.equal(registrations.length, 1, 'Hero Banner must register exactly once');
const bannerWidget = registrations[0];
assert.equal(bannerWidget.defaults().imageLayout, 'cover');
const normalizedNaturalLayout = bannerWidget.normalize({
    settings: { imageLayout: 'unsupported', imageLayoutTablet: 'natural', imageLayoutMobile: 'unsupported' },
}).settings;
assert.equal(normalizedNaturalLayout.imageLayout, 'cover');
assert.equal(normalizedNaturalLayout.imageLayoutTablet, 'natural');
assert.equal(normalizedNaturalLayout.imageLayoutMobile, '');

assert.match(config, /'hero_banner'\s*=>/);
assert.match(config, /widgets\/pro\/hero-banner\/definition\.js/);
assert.match(config, /widgets\/pro\/hero-banner\/Canvas\.vue/);
assert.match(config, /widgets\/pro\/hero-banner\/Settings\.vue/);
assert.match(config, /widgets\.pro\.hero-banner/);
assert.match(app, /hero_banner:\s*'Hero Banner'/);
assert.match(app, /hero_banner:\s*'fas fa-image'/);
assert.match(app, /video_playlist',\s*'hero_banner'/);

for (const marker of ['Content Behavior', 'Content Order', 'Add Button', 'Action Type', 'Video Source', 'Image Source', 'Image Layout', 'Natural Image Ratio', 'Responsive Position', 'Button Group Layout', 'Responsive Media', "editor.linkControl", 'editor.chooseMedia', "editor.settingsTab==='style'", "editor.settingsTab==='advanced'"]) {
  assert.ok(settings.includes(marker), `Settings must include ${marker}`);
}
assert.match(settings, /buttons\.length\s*>=\s*3/);
assert.match(settings, /buttons\.length\s*<=\s*1/);
assert.match(settings, /class="pb-widget-settings pb-widget-settings--general-new pb-widget-settings--pro pb-hero-settings"/);
assert.ok((settings.match(/class="pb-seg-btn"/g) || []).length >= 6, 'Hero segmented controls must reuse the v2.3 button class');
assert.match(settings, /const ResponsiveChoice\s*=/, 'Hero Banner button controls must use per-field responsive choices');
assert.match(settings, /const SizeControl\s*=/, 'Hero Banner button gap must use the shared responsive size pattern');
assert.match(settings, /buttonDirectionOptions:\s*\[\{\s*value:\s*'row',\s*label:\s*'Horizontal',\s*icon:\s*'fas fa-arrows-alt-h'/s);
assert.match(settings, /buttonDirectionOptions[\s\S]*value:\s*'column',\s*label:\s*'Vertical',\s*icon:\s*'fas fa-arrows-alt-v'/s);
assert.match(definition, /buttonAlignMode:\s*'inherit'/, 'Hero Banner buttons should follow the content alignment by default');
assert.match(definition, /buttonAlignModeTablet:\s*''/);
assert.match(definition, /buttonAlignModeMobile:\s*''/);
assert.match(settings, /buttonAlignmentValue/);
assert.match(settingsMarkup, /Follow Content Alignment/);
assert.match(settingsMarkup, /<responsive-choice label="Direction"/);
assert.match(settingsMarkup, /<responsive-choice label="Alignment"/);
assert.match(settingsMarkup, /<size-control label="Gap"/);
assert.match(settingsMarkup, /<responsive-choice label="Wrap Buttons"/);
const buttonGroupLayout = settingsMarkup.match(/<summary>Button Group Layout<\/summary>[\s\S]*?<\/details>/)?.[0] || '';
assert.ok(buttonGroupLayout, 'Button Group Layout section must be present');
assert.doesNotMatch(buttonGroupLayout, /<DeviceTabs :editor="editor" \/>/, 'Button Group Layout must not use one shared device tab');
assert.equal((settings.match(/<ToggleField/g) || []).length, 3, 'Hero content visibility controls must reuse one compact toggle component');
assert.doesNotMatch(settings, /pb-switch-row/, 'Hero settings must not render unstyled native checkbox rows');
assert.match(settings, /:deep\(\.pb-hero-devices button\)/);
assert.match(settings, /:deep\(\.pb-hero-devices button\)\{[^}]*gap:5px/, 'Hero responsive device tab icons and labels should have a consistent 5px gap');
assert.match(settings, /:deep\(\.pb-hero-devices span\)\{margin-left:0\}/, 'Hero responsive device tabs should not add a second label offset');
assert.match(settings, /:deep\(\.pb-hero-slider-responsive-choice [^)]*\.pb-seg-btn\)\{[^}]*gap:4px/, 'Hero Banner button group controls should keep an explicit icon-label gap');
assert.match(settings, /:deep\(\.pb-hero-slider-grid\)\{display:grid;grid-template-columns:minmax\(0,1fr\)/, 'Hero Banner position controls should use the Hero Slider single-column layout');
assert.match(settings, /:deep\(\.pb-hero-slider-grid \.pb-range\)\{[^}]*min-width:0/, 'Hero Banner position sliders should not collapse inside the value grid');
assert.equal((settings.match(/'has-action'/g) || []).length, 3, 'Hero media fields must mark only fields with a visible picker action');
assert.match(settings, /\.pb-hero-devices\{[^}]*padding:3px;[^}]*border:1px solid var\(--line\);[^}]*border-radius:9px;[^}]*background:var\(--soft\)/);
assert.match(settings, /:deep\(\.pb-hero-devices button\)\{[^}]*border:0!important;[^}]*border-radius:6px!important;[^}]*background:transparent/);
assert.match(settings, /:deep\(\.pb-hero-devices button\.active\)\{[^}]*background:#fff;[^}]*box-shadow:var\(--shadow-sm\)/);
assert.match(settings, /\.pb-media-field\.has-action input\{border-radius:6px 0 0 6px!important\}/);
assert.equal((settingsMarkup.match(/<ColorField /g) || []).length, 10, 'Hero color settings must all use the shared ColorField path');
assert.ok(settingsMarkup.includes('<input class="pb-input coloris pb-coloris-input" :value="modelValue"'), 'Hero ColorField must use the Coloris initializer hook');
assert.doesNotMatch(settingsMarkup, /<input type="color"/, 'Hero color settings must not render native color inputs');
assert.doesNotMatch(settingsMarkup, /pb-hero-color/, 'Hero color settings must not retain the native color wrapper');
assert.match(settings, /const PositionEditor =/);
assert.match(settings, /setAnchor\(anchor\)/);
assert.match(settings, /coordinates = \{/);
assert.match(settings, /set\('X', coordinates\[0\] \+ '%'\)/);
assert.match(settings, /pb-range-value-row/);
assert.match(settings, /pb-value-with-unit/);
assert.match(settings, /<responsive-menu :editor="editor"/);
assert.match(settings, /\.pb-hero-mode \.pb-seg-btn\{gap:5px\}/);
assert.match(settings, /:aria-pressed="contentVisible\(key\)"/);
assert.match(settings, /@click="toggleContentVisibility\(key\)"/);
assert.match(settings, /contentVisible\(key\)\{return this\.settings\[this\.visibilityKey\(key\)\]!==false;\}/);
assert.match(editorCss, /\.side-panel\.pb-panel\.left \.v23-properties-section :is\(\.pb-media-field\.has-action, \.pb-input-with-action, \.pb-hero-color\) > \.pb-input\s*\{[^}]*border-top-right-radius:\s*0 !important;[^}]*border-bottom-right-radius:\s*0 !important;/s);
assert.match(editorCss, /\.pb-panel\.left \.clr-field \.pb-coloris-input\s*\{[^}]*padding-right:\s*48px;/s);
assert.match(editorCss, /\.pb-panel\.left \.clr-field button\s*\{[^}]*width:\s*36px;/s);
assert.match(editorCss, /\.pb-panel\.left :is\(\.pb-basic-button-style-settings, \.pb-basic-text-style-settings, \.pb-basic-icon-style-settings\) \.clr-field button\s*\{[^}]*width:\s*36px;/s);
assert.doesNotMatch(editorCss, /\.pb-panel\.left \.clr-field button\s*\{[^}]*width:\s*48px;/s);
assert.doesNotMatch(editorCss, /\.pb-panel\.left :is\(\.pb-basic-button-style-settings, \.pb-basic-text-style-settings, \.pb-basic-icon-style-settings\) \.clr-field button\s*\{[^}]*width:\s*48px;/s);
assert.match(prototype, /\.color-control\s*\{[^}]*grid-template-columns:\s*36px 1fr;/s);
assert.match(prototype, /\.color-control input\[type="color"\]\s*\{[^}]*width:\s*36px;[^}]*height:\s*34px;/s);
assert.match(prototypeCss, /\.device-switcher\s*\{[^}]*border:\s*1px solid var\(--border\);[^}]*border-radius:\s*9px;[^}]*background:\s*#f7f8fc;/s);
assert.match(prototypeCss, /\.device-switcher button\.active\s*\{[^}]*background:\s*#fff;[^}]*box-shadow:/s);
assert.match(prototypeCss, /\.device-switcher button\s*\{[^}]*border:\s*0;[^}]*background:\s*transparent;/s);

assert.match(canvas, /data-hero-banner/);
assert.match(canvas, /contentOrder/);
assert.match(canvas, /positioningMode/);
assert.match(canvas, /PageBuilderElementorV23Runtime\?\.openMediaLightbox/);
assert.match(canvas, /responsiveValue\(/);
assert.match(canvas, /buttonAlignment\(\)\{/);
assert.match(canvas, /const align=this\.buttonAlignment;/, 'Hero Banner button layout should consume the computed alignment value');
assert.match(canvas, /target\+'Align'/, 'Hero Banner button alignment should resolve the grouped or independent alignment target dynamically');
assert.match(canvas, /is-natural-image/);
assert.match(canvas, /imageLayout\(\)/);
assert.ok(canvas.includes("percent(value,fallback){const raw=String(value??'').trim();"), 'Hero Banner Canvas must parse percentage position values');

assert.match(blade, /data-hero-banner/);
assert.match(blade, /data-hero-media/);
assert.match(blade, /buttonAlignMode/);
assert.match(blade, /alignTarget \. 'Align'/, 'Hero Banner Blade renderer should resolve grouped or independent button alignment dynamically');
assert.match(blade, /@media\(max-width:1024px\)/);
assert.match(blade, /@media\(max-width:767px\)/);
assert.match(frontendCss, /\.pb-hero-banner/);
assert.match(frontendV23Css, /\.pb-hero-banner\.is-natural-image/);
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
