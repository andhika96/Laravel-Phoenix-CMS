import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const source = readFileSync('public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js', 'utf8');
const sandbox = {
    Vue: { createApp(options) { return { mount() { return options; } }; } },
    document: { getElementById() { return { dataset: {} }; } },
    window: {}, URLSearchParams, console,
};
vm.createContext(sandbox);
vm.runInContext(`${source}\n;globalThis.options = ManageArticleTemplateVue3;`, sandbox);
const { methods, computed } = sandbox.options;

test('Mosaic draft defaults, custom values and zero survive opening and applying options', () => {
    const state = { activeTemplateKey: 'mosaic-magazine' };
    const defaults = methods.prepareTemplateOptions.call(state, {}, 'archive');
    assert.equal(defaults.mosaic_magazine.supporting_count, 2);
    assert.deepEqual(JSON.parse(JSON.stringify(defaults.mosaic_magazine.columns)), { desktop: 2, tablet: 2, mobile: 1 });
    for (const count of [0, 3, 4, 24]) {
        const prepared = methods.prepareTemplateOptions.call(state, { mosaic_magazine: {
            supporting_count: count, match_grid: true, columns: { desktop: 4, tablet: 3, mobile: 2 }, proportion: 'asymmetric',
        } }, 'archive');
        const session = {
            optionsModal: { key: 'mosaic-magazine', surface: 'archive', value: prepared },
            draft: { archive_template_options: {} }, cloneOptions: methods.cloneOptions,
            rebuildPreview() { this.refreshed = true; }, closeTemplateOptions() {},
        };
        methods.applyTemplateOptions.call(session);
        const saved = session.draft.archive_template_options['mosaic-magazine'];
        assert.equal(saved.mosaic_magazine.supporting_count, count);
        assert.equal(saved.mosaic_magazine.match_grid, true);
        assert.equal(saved.mosaic_magazine.columns.desktop, 4);
        assert.equal(saved.mosaic_magazine.proportion, 'asymmetric');
        assert.equal(session.refreshed, true);
    }
});

test('Mosaic section is restricted to its archive template', () => {
    const optionsModal = { value: { mosaic_magazine: {}, grid: {} } };
    const sections = (surface, activeTemplateKey) => computed.optionSections.call({ surface, activeTemplateKey, optionsModal });
    assert.ok(sections('archive', 'mosaic-magazine').some(section => section.key === 'mosaic-magazine'));
    assert.ok(!sections('archive', 'editorial-journal').some(section => section.key === 'mosaic-magazine'));
    assert.ok(!sections('detail', 'mosaic-magazine').some(section => section.key === 'mosaic-magazine'));
});

test('Mosaic settings and CMS accent reach the same preview URL', () => {
    const state = {
        surface: 'archive', activeTemplateKey: 'mosaic-magazine',
        previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
        buildPreviewUrl: methods.buildPreviewUrl,
        activeTemplateOptions: () => ({ mosaic_magazine: { supporting_count: 3, match_grid: true } }),
        activeThemeColor: () => '#1AA879',
    };
    methods.rebuildPreview.call(state);
    const url = new URL(state.previewUrl, 'https://example.test');
    assert.equal(url.searchParams.get('theme_color'), '#1AA879');
    assert.equal(JSON.parse(url.searchParams.get('template_options')).mosaic_magazine.supporting_count, 3);
});
