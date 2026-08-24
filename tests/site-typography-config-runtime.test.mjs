import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const source = readFileSync(
    path.join(process.cwd(), 'public/assets/js/vue3/manage_config/vueV3-manage-config-2026.js'),
    'utf8',
);

const loadMethods = (rootFontSize) => {
    let component;
    const context = vm.createContext({
        console,
        createApp(definition) {
            component = definition;

            return { mount() {} };
        },
        getComputedStyle() {
            return { fontSize: rootFontSize };
        },
        h() {},
        document: { documentElement: {} },
        window: { 'vue-select': {} },
    });

    vm.runInContext(source, context);

    return component.methods;
};

test('font unit conversion uses the browser root size instead of assuming sixteen pixels', () => {
    const methods = loadMethods('20px');
    const state = {
        responseData: {
            font_family: 'noto_sans',
            font_size: 20,
            font_size_unit: 'rem',
        },
        responseDataFont: [],
        siteTypography: { activeUnit: 'px' },
        ...methods,
    };

    methods.handleSiteTypographyUnitChange.call(state);

    assert.equal(state.responseData.font_size, 1);
    assert.equal(state.responseData.font_size_unit, 'rem');
});

test('em conversion uses the same computed browser root size', () => {
    const methods = loadMethods('20px');
    const state = {
        responseData: {
            font_family: 'noto_sans',
            font_size: 1,
            font_size_unit: 'px',
        },
        responseDataFont: [],
        siteTypography: { activeUnit: 'em' },
        ...methods,
    };

    methods.handleSiteTypographyUnitChange.call(state);

    assert.equal(state.responseData.font_size, 20);
    assert.equal(state.responseData.font_size_unit, 'px');
});

test('font unit conversion falls back to sixteen pixels when root size is invalid', () => {
    const methods = loadMethods('invalid');
    const state = {
        responseData: {
            font_family: 'noto_sans',
            font_size: 16,
            font_size_unit: 'rem',
        },
        responseDataFont: [],
        siteTypography: { activeUnit: 'px' },
        ...methods,
    };

    methods.handleSiteTypographyUnitChange.call(state);

    assert.equal(state.responseData.font_size, 1);
    assert.equal(state.responseData.font_size_unit, 'rem');
});
