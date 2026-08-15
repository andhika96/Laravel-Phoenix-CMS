import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => fs.existsSync(path.join(root, relative))
    ? fs.readFileSync(path.join(root, relative), 'utf8')
    : '';

const definition = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/definition.js');
const settings = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Settings.vue');
const canvas = read('public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Canvas.vue');
const blade = read('resources/views/pagebuilder_elementor_v23/widgets/pro/hero-slider.blade.php');
const config = read('config/pagebuilder_elementor_v23_widgets.php');
const app = read('public/js/pagebuilder_elementor_v23/app.js');
const runtime = read('public/js/pagebuilder_elementor_v23/frontend-runtime.js');
const frontendCss = read('public/assets/css/frontend_elementor.css');
const editorCss = read('public/assets/css/pagebuilder_elementor_v23.css');

assert.ok(definition, 'Hero Slider definition must exist');
assert.match(definition, /type:\s*['"]hero_slider['"]/);
assert.match(definition, /label:\s*['"]Hero Slider['"]/);
assert.match(definition, /category:\s*['"]pro['"]/);
assert.match(definition, /mediaType/);
assert.match(definition, /videoAutoplay/);
assert.match(definition, /videoDurationMode/);
assert.match(definition, /dailymotion/);

const registrations = [];
vm.runInNewContext(definition, {
    window: {
        PageBuilderElementorV23ComplexWidgetRuntime: {
            image_box: {
                defaults: () => ({
                    position: 'default',
                    horizontalOrientation: 'left',
                    verticalOrientation: 'top',
                    positionX: '0px',
                    positionY: '0px',
                    cssId: '',
                    cssClass: '',
                }),
            },
        },
        PageBuilderElementorV23Widgets: {
            register(definitionValue) {
                registrations.push(definitionValue);
            },
        },
    },
});
assert.equal(registrations.length, 1, 'Hero Slider must register exactly once');
const widget = registrations[0];
const defaults = widget.defaults();
assert.equal(widget.type, 'hero_slider');
assert.ok(Array.isArray(defaults.slides) && defaults.slides.length >= 2);
assert.equal(defaults.slides[0].mediaType, 'image');
assert.equal(defaults.slides[0].videoAutoplay, 'inherit');
assert.equal(defaults.videoDurationMode, 'interval');
assert.equal(defaults.direction, 'horizontal');
assert.equal(defaults.paginationPositionHorizontal, 'bottom-center');
assert.equal(defaults.paginationPositionVertical, 'center-right');
assert.equal(defaults.position, 'default');
assert.equal(defaults.horizontalOrientation, 'left');
assert.equal(defaults.verticalOrientation, 'top');
assert.equal(defaults.positionX, '0px');
assert.equal(defaults.positionY, '0px');

const normalizedNode = widget.normalize({
    type: 'hero_slider',
    settings: {
        direction: 'diagonal',
        videoDurationMode: 'watch-video',
        autoplaySpeed: 999999,
        transitionSpeed: 999999,
        perMove: 0,
        position: 'floating',
        horizontalOrientation: 'diagonal',
        verticalOrientation: 'middle',
        slides: [
            { id: 'image', mediaType: 'image', imageUrl: '/hero.webp' },
            { id: 'youtube', mediaType: 'video', videoProvider: 'youtube', videoUrl: 'https://youtu.be/demo123' },
            { id: 'unknown', mediaType: 'video', videoProvider: 'unknown-provider', videoUrl: 'https://example.com/embed/demo' },
        ],
    },
});
assert.equal(normalizedNode.settings.direction, 'horizontal', 'invalid direction must use horizontal fallback');
assert.equal(normalizedNode.settings.videoDurationMode, 'interval', 'invalid duration mode must use interval fallback');
assert.equal(normalizedNode.settings.slides[1].videoAutoplay, 'inherit');
assert.equal(normalizedNode.settings.slides[2].videoProvider, 'embed', 'unknown providers must use generic embed fallback');
assert.equal(normalizedNode.settings.slides.length, 3);
assert.equal(normalizedNode.settings.position, 'default', 'Advanced Position must default to shared Default');
assert.equal(normalizedNode.settings.horizontalOrientation, 'left');
assert.equal(normalizedNode.settings.verticalOrientation, 'top');
assert.equal(normalizedNode.settings.autoplaySpeed, 60000);
assert.equal(normalizedNode.settings.transitionSpeed, 10000);
assert.equal(normalizedNode.settings.perMove, 1);

assert.match(config, /'hero_slider'\s*=>/);
assert.match(config, /widgets\/pro\/hero-slider\/definition\.js/);
assert.match(config, /widgets\/pro\/hero-slider\/Canvas\.vue/);
assert.match(config, /widgets\/pro\/hero-slider\/Settings\.vue/);
assert.match(config, /widgets\.pro\.hero-slider/);
assert.match(app, /hero_slider:\s*'Hero Slider'/);
assert.match(app, /hero_slider:\s*'fas fa-photo-video'/);
assert.match(app, /video_playlist',\s*'hero_banner',\s*'hero_slider'/);

for (const marker of [
    'Slides', 'Add Slide', 'Media Type', 'Video Provider', 'Video URL', 'Poster Image',
  'Global Video Autoplay', 'Video Duration Mode', 'Video Autoplay Fallback',
    'Horizontal', 'Vertical', 'Horizontal Slider Position', 'Vertical Slider Position',
  'Slides per move', 'Rewind at end', 'Lazy-load media', 'Adaptive Height', 'Fixed Height', 'Minimum Height', 'Vertical Padding', 'DeviceTabs',
  "editor.settingsTab==='content'", "editor.settingsTab==='style'", "editor.settingsTab==='advanced'",
]) {
    assert.ok(settings.includes(marker), `Hero Slider settings must include ${marker}`);
}
assert.match(settings, /class="pb-input coloris pb-coloris-input"/);
assert.doesNotMatch(settings, /<input type="color"/);
assert.match(settings, /const SizeControl\s*=/);
assert.match(settings, /const ScalarControl\s*=/);
assert.match(settings, /<size-control label="Button Radius"/);
assert.match(settings, /<size-control label="Horizontal Padding"/);
assert.match(settings, /<size-control label="Vertical Padding"/);
assert.match(settings, /<size-control[^>]*label="Fixed Height"/);
assert.match(settings, /<size-control[^>]*label="Minimum Height"/);
assert.match(settings, /<scalar-control[^>]*label="Interval \(ms\)"/);
assert.match(settings, /<scalar-control[^>]*label="Transition Speed"/);
assert.match(settings, /<scalar-control[^>]*label="Slides per move"/);
assert.match(settings, /allowed-units/);
assert.match(settings, /pb-hero-slider-timing-controls/);
assert.match(settings, /Unsupported provider or SDK errors use the slider interval/);
assert.doesNotMatch(settings, /Video Autoplay Fallback<\/label><select/);
assert.doesNotMatch(settings, /v-model="settings\.fixedHeight"/);
assert.doesNotMatch(settings, /v-model="settings\.autoplaySpeed"/);
assert.doesNotMatch(settings, /v-model="settings\.transitionSpeed"/);
assert.doesNotMatch(settings, /v-model="settings\.perMove"/);
assert.match(settings, /aria-label.*Choose/);
assert.match(settings, /editor\.sizeControlDisplayValue/);
assert.match(settings, /editor\.setSizeControlUnit/);
assert.match(settings, /paginationPositionHorizontal/);
assert.match(settings, /paginationPositionVertical/);
assert.match(canvas, /data-hero-slider/);
assert.match(canvas, /data-hero-slide/);
assert.match(canvas, /videoProvider/);
assert.match(canvas, /responsiveValue\(/);
assert.match(canvas, /paginationPosition\(/);
assert.match(canvas, /data-position/);

assert.match(blade, /data-hero-slider/);
assert.match(blade, /data-hero-slider-config/);
assert.match(blade, /data-hero-slide/);
assert.match(blade, /data-hero-video/);
assert.match(blade, /paginationPositionHorizontal/);
assert.match(blade, /paginationPositionVertical/);
assert.match(blade, /data-position=/);
assert.match(blade, /youtube-nocookie\.com/);
assert.match(blade, /player\.vimeo\.com\/video/);
assert.match(blade, /dailymotion\.com\/embed\/video/);
assert.match(blade, /playsinline/);
assert.match(blade, /adaptive/);

assert.match(runtime, /function initHeroSlider\(/);
assert.match(runtime, /function loadHeroSliderScript\(/);
assert.match(runtime, /YouTube IFrame API|youtube\.com\/iframe_api/);
assert.match(runtime, /player\.vimeo\.com\/api\/player\.js/);
assert.match(runtime, /Dailymotion Web SDK|dailymotionSdkUrl|PLAYER_END/);
assert.match(runtime, /data-hero-video-control/);
assert.match(runtime, /currentTime/);
assert.match(runtime, /videoDurationMode/);
assert.match(runtime, /ArrowUp|ArrowDown/);
assert.match(runtime, /paginationPositionHorizontal/);
assert.match(runtime, /data-position/);
assert.match(runtime, /data-hero-slider/);
assert.match(runtime, /initHeroSlider/);
assert.match(runtime, /PageBuilderElementorV23Runtime\s*=\s*Object\.freeze\(\{[^}]*initHeroSlider/s);

assert.match(frontendCss, /\.pb-hero-slider/);
assert.match(frontendCss, /\.pb-hero-slider\[data-direction="vertical"\]/);
assert.match(frontendCss, /\.pb-hero-slider__pagination\[data-orientation="horizontal"\]\[data-position="bottom-center"\]/);
assert.match(frontendCss, /\.pb-hero-slider__pagination\[data-orientation="vertical"\]\[data-position="center-right"\]/);
assert.match(frontendCss, /prefers-reduced-motion/);
assert.match(editorCss, /\.pb-node\.pb-node-hero_slider \[data-pb-interactive="true"\]/);
assert.match(editorCss, /\.side-panel\.pb-panel\.left \.v23-properties-section \.pb-widget-settings\.pb-hero-slider-settings/);
assert.match(editorCss, /\.pb-hero-slider-settings \.pb-media-field\.has-action\s*\{[\s\S]*?display:\s*flex/);
assert.match(editorCss, /\.pb-hero-slider-settings \.pb-media-field\.has-action > \.pb-input/);
assert.match(editorCss, /\.pb-hero-slider-settings \.pb-media-field\.has-action > button/);
assert.match(editorCss, /\.pb-hero-slider-settings \.pb-hero-devices/);
assert.match(editorCss, /\.pb-hero-slider-settings \.pb-widget-advanced-controls \.pb-value-with-unit/);
assert.match(editorCss, /\.pb-hero-slider-slide__header > i[\s\S]*margin-right/);
assert.match(editorCss, /\.pb-hero-slider-slide__header > strong[\s\S]*font-size:\s*13px/);
assert.match(editorCss, /\.pb-hero-slider-settings[\s\S]*\.pb-seg-btn[\s\S]*gap:\s*4px/);
assert.match(editorCss, /\.pb-hero-slider-settings \.pb-range-value-row/);

console.log('pagebuilder v2.3 hero slider widget parity test passed');
