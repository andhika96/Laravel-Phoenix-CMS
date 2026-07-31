import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const definition = readFileSync('public/js/pagebuilder_elementor/widgets/basic/heading/definition.js', 'utf8');
const settings = readFileSync('public/js/pagebuilder_elementor/widgets/basic/heading/Settings.vue', 'utf8');
const canvas = readFileSync('public/js/pagebuilder_elementor/widgets/basic/heading/Canvas.vue', 'utf8');
const app = readFileSync('public/js/pagebuilder_elementor/app.js', 'utf8');
const blade = readFileSync('resources/views/pagebuilder_elementor/widgets/basic/heading.blade.php', 'utf8');

for (const marker of [
    "linkUrl: ''",
    'linkNofollow: false',
    'dynamicBindings:',
    "headingFontFamily: 'inherit'",
    "headingTextStrokeWidth: '0px'",
    "headingTextShadow: 'none'",
    "blendMode: 'normal'",
    'hoverTransitionDuration: 0.3',
]) {
    assert.ok(definition.includes(marker), `Heading definition must include ${marker}`);
}

for (const marker of [
    'editor.dynamicTagControl',
    'editor.linkControl',
    'editor.typographyControl',
    'editor.textStrokeControl',
    'editor.textShadowControl',
    'editor.widgetAdvancedControls',
    "value:'justify',icon:'fas fa-align-justify'",
    'Blend Mode',
    'Link Color',
]) {
    assert.ok(settings.includes(marker), `Heading Settings must include ${marker}`);
}

assert.ok(canvas.includes('safeLinkUrl'), 'Heading Canvas must sanitize link URLs');
assert.ok(canvas.includes('heading-title-link'), 'Heading Canvas must render optional linked headings');
assert.ok(app.includes("this.node.type === 'heading'"), 'Editor shell must apply shared Advanced controls to Heading');
assert.ok(blade.includes('WidgetAdvancedStyleResolver'), 'Heading frontend must use the shared Advanced resolver');
