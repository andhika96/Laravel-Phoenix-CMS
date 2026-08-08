import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';
import vm from 'node:vm';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/app.js'), 'utf8');
const css = readFileSync(resolve(root, 'public/assets/css/pagebuilder_elementor_v23.css'), 'utf8');
const advancedControls = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/widgets/shared/AdvancedControls.vue'), 'utf8');

function contextualHelpers() {
    const source = app.match(/\/\/ V23_CONTEXTUAL_PROPERTY_HELPERS_START([\s\S]*?)\/\/ V23_CONTEXTUAL_PROPERTY_HELPERS_END/)?.[1];
    assert.ok(source, 'v2.3 contextual property helpers should exist');

    const context = {
        isCont: (type) => type === 'container' || type === 'container_fluid',
        isGrid: (type) => type === 'grid' || type === 'row_grid',
    };
    vm.runInNewContext(`${source}\nthis.resetPropertiesPanelScroll = resetPropertiesPanelScroll;`, context);
    return context;
}

test('property tabs reset the shared sidebar scroll position', () => {
    const panel = { scrollTop: 480 };
    const context = contextualHelpers();

    context.resetPropertiesPanelScroll({
        querySelector(selector) {
            assert.equal(selector, '.side-panel.left-panel .panel-body');
            return panel;
        },
    });

    assert.equal(panel.scrollTop, 0);
    assert.match(app, /function selectSettingsTab\(tabId\)[\s\S]*?settingsTab\.value\s*=\s*tabId;[\s\S]*?nextTick\(resetPropertiesPanelScroll\);/);
    assert.match(app, /@click="selectSettingsTab\(tab\.id\)"/);
    assert.match(app, /watch\(selectedId,[\s\S]*?nextTick\(resetPropertiesPanelScroll\);/);
});

test('the v2.3 properties panel uses a readable and consistent density contract', () => {
    const inlineControlSelector = '.pb-form-group:not(:is(.pb-form-row--two, .pb-two-column-row, .pb-inline-fields) > .pb-form-group):has(> .pb-form-label + :is(.pb-select, .pb-color-row, .pb-btn-group, .pb-coloris-input, .clr-field))';

    assert.match(css, /\.workspace\s*\{[\s\S]*?grid-template-columns:\s*300px minmax\(440px, 1fr\);/);
    assert.match(css, /\.property-tab\s*\{[\s\S]*?height:\s*48px;[\s\S]*?font-size:\s*12px;/);
    assert.match(css, /\.property-tab:focus\s*\{\s*outline:\s*none;\s*\}/);
    assert.match(css, /\.property-tab:focus-visible\s*\{[^}]*outline:\s*2px solid var\(--brand\);/);
    assert.match(css, /\.selection-summary-icon\s*\{[\s\S]*?width:\s*36px;[\s\S]*?height:\s*36px;/);
    assert.match(css, /\.selection-summary strong\s*\{[\s\S]*?font-size:\s*12px;/);
    assert.match(css, /\.selection-summary small\s*\{[\s\S]*?font-size:\s*10px;/);
    assert.match(css, /\.side-panel \.v23-properties-section \.pb-collapsible > summary\s*\{[\s\S]*?min-height:\s*40px;[\s\S]*?font-size:\s*12px;/);
    assert.match(css, /\.side-panel \.v23-properties-section \.pb-collapsible > summary:focus-visible\s*\{[^}]*outline:\s*2px solid var\(--brand\);/);
    assert.match(css, /\.side-panel \.v23-properties-section \.pb-collapsible-body\s*\{\s*padding:\s*8px 0 10px !important;\s*\}/);
    assert.match(css, /\.side-panel\.pb-panel\.left \.v23-properties-section \.pb-widget-settings \.pb-form-label\s*\{[\s\S]*?font-size:\s*11px;/);
    assert.match(css, /\.side-panel\.pb-panel\.left \.v23-properties-section \.pb-widget-settings :is\(\.pb-input, \.pb-select\)\s*\{[\s\S]*?min-height:\s*36px;[\s\S]*?font-size:\s*12px;/);
    assert.ok(css.includes(inlineControlSelector));
});

test('shared heading and Advanced controls keep compact but legible proportions', () => {
    assert.match(css, /\.pb-panel\.left \.pb-heading-style-settings \.pb-heading-choice-row\s*\{[\s\S]*?grid-template-columns:\s*minmax\(0, 1fr\) 152px;/);
    assert.match(css, /\.pb-panel\.left \.pb-heading-style-settings \.clr-field button\s*\{[\s\S]*?width:\s*34px;/);
    assert.match(advancedControls, /\.pb-ai-disabled strong\s*\{\s*font-size:\s*12px;[\s\S]*?line-height:\s*1\.3;\s*\}/);
    assert.match(advancedControls, /\.pb-ai-disabled small\s*\{\s*font-size:\s*10px;[\s\S]*?line-height:\s*1\.4;\s*\}/);
});
