import assert from 'node:assert/strict';
import test from 'node:test';
import { readdirSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';
import vm from 'node:vm';
import {
    pageBuilderV24CapabilityPrelude,
    readPageBuilderV24EditorStyles,
} from './helpers/pagebuilder-v24-module-source.mjs';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v24/app.js'), 'utf8');
const css = readPageBuilderV24EditorStyles(root);

function collectFiles(directory, suffix) {
    return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const path = resolve(directory, entry.name);
        return entry.isDirectory() ? collectFiles(path, suffix) : (entry.name.endsWith(suffix) ? [path] : []);
    });
}

const widgetRoot = resolve(root, 'resources/pagebuilder_elementor_v24/modules');
const widgetSources = collectFiles(widgetRoot, 'Settings.vue')
    .map((path) => readFileSync(path, 'utf8'))
    .join('\n');
const widgetVueSources = collectFiles(widgetRoot, '.vue')
    .map((path) => readFileSync(path, 'utf8'))
    .join('\n');
const sharedAdvancedSource = readFileSync(resolve(root, 'resources/pagebuilder_elementor_v24/shared/AdvancedControls.vue'), 'utf8');

const unitSliderDefinitions = [
    ['Accordion dimensions', 'widgets/general/accordion/Settings.vue', 'pb-accordion-dimension-control'],
    ['Tabs dimensions', 'widgets/general/tabs/Settings.vue', 'pb-tabs-dimension-control'],
    ['Text Editor dimensions', 'widgets/basic/text-editor/Settings.vue', 'pb-basic-dimension-control'],
    ['Icon dimensions', 'widgets/basic/icon/Settings.vue', 'pb-basic-dimension-control'],
    ['Button icon spacing', 'widgets/basic/button/Settings.vue', 'pb-basic-button-icon-spacing-row'],
].map(([label, path, marker]) => ({
    label,
    marker,
    source: readFileSync(resolve(widgetRoot, path), 'utf8'),
})).concat([
    ['Advanced dimensions', 'AdvancedControls.vue', 'pb-advanced-dimension-control'],
    ['Typography dimensions', 'controls/TypographyControl.vue', 'pb-typography-dimension'],
].map(([label, path, marker]) => ({
    label,
    marker,
    source: readFileSync(resolve(root, 'resources/pagebuilder_elementor_v24/shared', path), 'utf8'),
})));

function contextualHelpers() {
    const source = app.match(/\/\/ V24_CONTEXTUAL_PROPERTY_HELPERS_START([\s\S]*?)\/\/ V24_CONTEXTUAL_PROPERTY_HELPERS_END/)?.[1];
    assert.ok(source, 'v2.4 contextual property helpers should exist');

    const context = {};
    vm.runInNewContext(`${pageBuilderV24CapabilityPrelude(root)}\n${source}\nthis.resetPropertiesPanelScroll = resetPropertiesPanelScroll;`, context);
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

test('the v2.4 properties panel matches the approved prototype density contract', () => {
    const contractMarker = css.match(/\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_START \*\/([\s\S]*?)\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_END \*\//)?.[1];
    assert.ok(contractMarker, 'the isolated v2.4 prototype properties contract should exist');
    const contract = css;

    assert.match(css, /\.workspace\s*\{[\s\S]*?grid-template-columns:\s*300px minmax\(440px, 1fr\);/);
    assert.match(contract, /\.property-tab\s*\{[^}]*height:\s*42px;[^}]*font-size:\s*10px;/);
    assert.match(css, /\.property-tab:focus\s*\{\s*outline:\s*none;\s*\}/);
    assert.match(css, /\.property-tab:focus-visible\s*\{[^}]*outline:\s*2px solid var\(--brand\);/);
    assert.match(contract, /\.selection-summary\s*\{[^}]*margin-bottom:\s*14px;[^}]*padding:\s*10px;/);
    assert.match(contract, /\.selection-summary-icon\s*\{[^}]*width:\s*33px;[^}]*height:\s*33px;/);
    assert.match(contract, /\.selection-summary strong\s*\{[^}]*font-size:\s*10px;/);
    assert.match(contract, /\.selection-summary small\s*\{[^}]*font-size:\s*8\.5px;/);
    assert.match(contract, /\.pb-collapsible > summary\s*\{[^}]*min-height:\s*34px(?:\s*!important)?;[^}]*font-size:\s*10px(?:\s*!important)?;/);
    assert.match(css, /\.side-panel \.v24-properties-section \.pb-collapsible > summary:focus-visible\s*\{[^}]*outline:\s*2px solid var\(--brand\);/);
    assert.match(contract, /\.pb-form-group\s*\{[^}]*margin-bottom:\s*12px(?:\s*!important)?;/);
    assert.match(contract, /\.pb-form-label\s*\{[^}]*margin-bottom:\s*6px;[^}]*font-size:\s*9\.5px;[^}]*font-weight:\s*600;/);
    assert.match(contract, /:is\(\.pb-input, \.pb-select\)\s*\{[^}]*height:\s*34px;[^}]*font-size:\s*10px(?:\s*!important)?;/);
    assert.match(contract, /:is\(\.pb-textarea, textarea\.pb-input\)\s*\{[^}]*min-height:\s*76px;[^}]*font-size:\s*10px;/);
    assert.match(contract, /:is\(\.pb-seg-group,[^)]*\.pb-state-tabs[^)]*\)\s*\{[^}]*padding:\s*3px;/);
    assert.match(contract, /:is\(\.pb-seg-btn, \.pb-state-tabs button\)\s*\{[^}]*height:\s*27px(?:\s*!important)?;[^}]*font-size:\s*10px(?:\s*!important)?;/);
    assert.match(contract, /\.pb-range-value-row\s*\{[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 92px(?:\s*!important)?;[^}]*gap:\s*8px;/);
    assert.match(contract, /\.pb-range-value-row > :is\(\.pb-input-compact, \.pb-range-number\)\s*\{[^}]*width:\s*72px;[^}]*justify-self:\s*end;/);
    assert.match(contract, /\.pb-grid-gap-controls \.pb-range-value-row\s*\{[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 54px 36px(?:\s*!important)?;/);
    assert.match(contract, /\.pb-value-with-unit\s*\{[^}]*min-height:\s*30px;/);
    assert.match(contract, /\.pb-value-with-unit \.pb-input\s*\{[^}]*width:\s*100%(?:\s*!important)?;[^}]*min-width:\s*0(?:\s*!important)?;/);
    assert.match(contract, /\.pb-toggle-state\s*\{[^}]*position:\s*absolute;[^}]*clip:\s*rect\(0 0 0 0\);/);
    assert.doesNotMatch(contract, /\.pb-toggle-state\s*\{[^}]*display:\s*none;/);
    assert.doesNotMatch(contractMarker, /:has\(/, 'prototype controls stay vertically stacked instead of forcing label/control rows');
});

test('shared buttons and compound fields inherit the prototype proportions', () => {
    const contractMarker = css.match(/\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_START \*\/([\s\S]*?)\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_END \*\//)?.[1];
    assert.ok(contractMarker);
    const contract = css;

    assert.match(css, /\.pb-panel\.left \.pb-heading-style-settings \.pb-heading-choice-row\s*\{[\s\S]*?grid-template-columns:\s*minmax\(0, 1fr\) 152px;/);
    assert.match(css, /\.pb-panel\.left \.pb-heading-style-settings \.clr-field button\s*\{[\s\S]*?width:\s*34px;/);
    assert.match(contract, /\.pb-ai-disabled strong\s*\{[^}]*font-size:\s*10px/);
    assert.match(contract, /\.pb-ai-disabled small\s*\{[^}]*font-size:\s*8\.5px/);
    assert.match(contract, /\.pb-typography-trigger\s*\{[^}]*width:\s*30px;[^}]*height:\s*30px;/);
    assert.match(contract, /:is\(\.pb-text-effect-trigger, \.pb-css-filter-trigger\)\s*\{[^}]*width:\s*30px;[^}]*height:\s*28px;/);
    assert.match(contract, /\.pb-icon-picker-field\s*\{[^}]*grid-template-columns:\s*34px minmax\(0, 1fr\) 10px;/);
    assert.match(contract, /:is\(\.pb-carousel-gallery__add, \.pb-basic-gallery-picker__add, \.pb-social-add/);
});

test('Accordion item fields, responsive tools, and standalone color swatches keep compact visual alignment', () => {
    assert.match(css, /\.pb-accordion-settings \.pb-accordion-item-fields\s*\{[^}]*padding:\s*10px 10px 12px;/);
    assert.match(css, /:is\(\.pb-tabs-settings, \.pb-accordion-settings\)[^{}]*\.pb-label-row-device > \.pb-label-tools\s*\{[^}]*flex:\s*0 0 auto;[^}]*margin-left:\s*auto;[^}]*justify-content:\s*flex-end;/);
    assert.match(css, /:is\(\.pb-tabs-settings, \.pb-accordion-settings\)[^{}]*\.clr-field\s*\{[^}]*overflow:\s*hidden;[^}]*border-radius:\s*7px;/);
    assert.match(css, /:is\(\.pb-tabs-settings, \.pb-accordion-settings\)[^{}]*\.clr-field button\s*\{[^}]*width:\s*36px;[^}]*height:\s*30px;[^}]*border-radius:\s*7px;/);
});

test('every unit-bearing slider groups its numeric input and unit beside the range', () => {
    for (const definition of unitSliderDefinitions) {
        const line = definition.source.split(/\r?\n/).find((candidate) => candidate.includes(definition.marker) && candidate.includes('type="range"') && candidate.includes('pb-mini-unit'));
        assert.ok(line, `${definition.label} should expose a unit-bearing range template`);

        const rangeIndex = line.indexOf('type="range"');
        assert.doesNotMatch(line.slice(0, rangeIndex), /<select class="pb-mini-unit"/, `${definition.label} must not leave the unit in the heading row`);
        assert.match(
            line.slice(rangeIndex),
            /<div class="pb-value-with-unit"><input[^>]*type="number"[^>]*><select class="pb-mini-unit"/,
            `${definition.label} should keep its number and unit in one trailing group`,
        );
    }

    const contractMarker = css.match(/\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_START \*\/([\s\S]*?)\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_END \*\//)?.[1];
    assert.ok(contractMarker);
    const contract = css;
    assert.match(contract, /\.pb-value-with-unit\s*\{[^}]*display:\s*grid;[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 36px\s*!important;/);
    assert.match(contract, /\.pb-typography-range-row\s*\{[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 92px;/);
    assert.match(css, /\.pb-basic-button-icon-spacing-row\s*\{[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 92px;/);
});

test('shared numeric and compound controls stay compact across every v2.4 widget', () => {
    const contractMarker = css.match(/\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_START \*\/([\s\S]*?)\/\* V24_PROTOTYPE_PROPERTIES_CONTRACT_END \*\//)?.[1];
    assert.ok(contractMarker);
    const contract = css;

    assert.match(contract, /input\[type="number"\]\s*\{[^}]*appearance:\s*auto\s*!important;[^}]*-moz-appearance:\s*auto\s*!important;/);
    assert.match(contract, /input\[type="number"\]::-webkit-inner-spin-button,[^}]*input\[type="number"\]::-webkit-outer-spin-button\s*\{[^}]*-webkit-appearance:\s*auto\s*!important;[^}]*opacity:\s*1\s*!important;/);

    assert.match(contract, /:is\(\.pb-four-sides-with-link, \.pb-advanced-edge-fields\)\s*\{[^}]*grid-template-columns:\s*repeat\(4, minmax\(0, 1fr\)\) 28px;/);
    assert.match(contract, /:is\(\.pb-four-sides-with-link, \.pb-advanced-edge-fields\)[^}]*input\[type="number"\]\s*\{[^}]*height:\s*30px(?:\s*!important)?;[^}]*padding:\s*0 3px(?:\s*!important)?;[^}]*border-right-width:\s*0;/);
    assert.match(contract, /:is\(\.pb-four-sides-with-link, \.pb-advanced-edge-fields\)[^}]*\.pb-link-btn\s*\{[^}]*width:\s*28px;[^}]*height:\s*30px;/);
    assert.match(contract, /:is\(\.pb-four-sides-with-link, \.pb-advanced-edge-fields\)[^}]*\.pb-link-btn i\s*\{[^}]*font-size:\s*9px(?:\s*!important)?;/);
    assert.match(contract, /\.pb-container-gap-control__values\s*\{[^}]*grid-template-columns:\s*repeat\(2, minmax\(0, 1fr\)\) 28px(?:\s*!important)?;[^}]*gap:\s*0;/);
    assert.match(contract, /\.pb-container-gap-control__values \.pb-input\s*\{[^}]*height:\s*30px(?:\s*!important)?;[^}]*border-right-width:\s*0;/);
    assert.match(contract, /\.pb-container-gap-control__values \.pb-link-btn\s*\{[^}]*width:\s*28px;[^}]*height:\s*30px;/);
    assert.match(contract, /\.pb-container-gap-control__values \.pb-link-btn i\s*\{[^}]*font-size:\s*9px(?:\s*!important)?;/);
    assert.match(contract, /\.pb-four-sides:not\(\.pb-four-sides-with-link\)\s*\{[^}]*grid-template-columns:\s*repeat\(4, minmax\(0, 1fr\)\);[^}]*gap:\s*6px;/);
    assert.match(contract, /\.pb-four-sides:not\(\.pb-four-sides-with-link\)[^}]*input\[type="number"\]\s*\{[^}]*height:\s*30px(?:\s*!important)?;[^}]*border-radius:\s*7px;/);

    assert.match(contract, /\.pb-link-control-row\s*\{[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 34px;/);
    assert.match(contract, /\.pb-link-control-row \.pb-input\s*\{[^}]*border-radius:\s*8px 0 0 8px(?:\s*!important)?;[^}]*border-right:\s*0;/);
    assert.match(contract, /\.pb-link-options-trigger\s*\{[^}]*width:\s*34px;[^}]*min-height:\s*34px;[^}]*border-radius:\s*0 8px 8px 0;/);

    assert.match(contract, /\.pb-typography-popover\s*\{[^}]*margin-top:\s*6px;[^}]*padding:\s*10px;/);
    assert.match(contract, /\.pb-typography-popover-head\s*\{[^}]*margin-bottom:\s*6px;[^}]*font-size:\s*10px;/);
    assert.match(contract, /\.pb-typography-field\s*\{[^}]*margin-bottom:\s*6px;/);
    assert.match(contract, /\.pb-typography-select-field\s*\{[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 108px;[^}]*margin-bottom:\s*6px;[^}]*font-size:\s*10px;/);
    assert.match(contract, /\.pb-typography-dimension\s*\{[^}]*margin-bottom:\s*6px(?:\s*!important)?;/);
    assert.match(contract, /\.pb-typography-dimension-head\s*\{[^}]*margin-bottom:\s*2px;[^}]*font-size:\s*10px;/);
    assert.match(contract, /\.pb-typography-range-row\s*\{[^}]*grid-template-columns:\s*minmax\(0, 1fr\) 92px;[^}]*gap:\s*6px;/);
    assert.match(contract, /:is\(\.pb-text-effect-popover, \.pb-css-filter-popover, \.pb-link-options-popover\)\s*\{[^}]*gap:\s*8px(?:\s*!important)?;[^}]*padding:\s*10px(?:\s*!important)?;/);
    assert.match(contract, /\.pb-value-with-unit \.pb-mini-unit\s*\{[^}]*width:\s*36px;[^}]*min-width:\s*36px;/);
});

test('the shared density contract covers every active four-side and three-cell range shape', () => {
    const classValues = [...widgetSources.matchAll(/class="([^"]*)"/g)].map((match) => match[1].split(/\s+/));
    const plainSides = classValues.filter((tokens) => tokens.includes('pb-four-sides') && !tokens.includes('pb-four-sides-with-link'));
    const linkedSides = classValues.filter((tokens) => tokens.includes('pb-four-sides-with-link'));

    assert.equal(plainSides.length, 4, 'all four standalone module-owned four-side controls remain covered');
    assert.equal(linkedSides.length, 33, 'all active module-owned linked four-side controls remain covered');
    assert.equal((sharedAdvancedSource.match(/pb-advanced-edge-fields/g) || []).length >= 1, true, 'shared Advanced edge controls remain covered');
    assert.equal((widgetSources.match(/pb-container-gap-control__values/g) || []).length, 4, 'Container and Container Fluid expose all four linked gap controls');
    assert.equal((widgetSources.match(/pb-range-number/g) || []).length, 4, 'Grid and Row Grid expose four direct three-cell range rows');
});

test('every responsive spacing side uses a numeric input with native steppers', () => {
    const spacingInputs = [...widgetSources.matchAll(/<input class="pb-input"[^>]*spacingSideValue[^>]*>/g)]
        .map((match) => match[0]);
    const sideInputs = [...widgetVueSources.matchAll(/<(?:label|div)[^>]*class="[^"]*pb-side-input[^"]*"[^>]*><input class="pb-input"[^>]*>/g)]
        .map((match) => match[0]);

    assert.equal(spacingInputs.length, 4, 'all four layout-module spacing input templates are audited');
    spacingInputs.forEach((input) => assert.match(input, /\btype="number"/, input));
    assert.equal(sideInputs.length, 33, 'all active module-owned side-input definitions are audited');
    sideInputs.forEach((input) => assert.match(input, /\btype="number"/, input));
    assert.match(sharedAdvancedSource, /class="pb-advanced-edge-fields"[\s\S]*?type="number"/, 'the single shared Advanced file must keep numeric edge controls');
});
