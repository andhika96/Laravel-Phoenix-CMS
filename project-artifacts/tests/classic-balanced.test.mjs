import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const source = readFileSync('public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js', 'utf8');
const sandbox = { Vue: { createApp(options) { return { mount() { return options; } }; } }, document: { getElementById() { return { dataset: {} }; } }, window: {}, URLSearchParams, console };
vm.createContext(sandbox);
vm.runInContext(`${source}\n;globalThis.options = ManageArticleTemplateVue3;`, sandbox);
const { methods, computed } = sandbox.options;

test('percentage gap, padding and radius survive the manager draft', () => {
    const prepared = methods.prepareTemplateOptions.call({ activeTemplateKey: 'balanced-card-grid' }, {
        balanced_grid: { gap: { desktop: '5%' }, card_padding: '4%', card_radius: '10%' },
    }, 'archive');
    assert.equal(prepared.balanced_grid.gap.desktop, '5%');
    assert.equal(prepared.balanced_grid.card_padding, '4%');
    assert.equal(prepared.balanced_grid.card_radius, '10%');
});

for (const [template, key] of [['mosaic-classic', 'mosaic_classic'], ['balanced-card-grid', 'balanced_grid']]) {
    test(`${template} normalizes defaults and passes custom settings to preview and draft`, () => {
        const state = { activeTemplateKey: template };
        const prepared = methods.prepareTemplateOptions.call(state, { [key]: {
            side_count: 0, show_excerpt: false, show_date: false, gap: { desktop: '3rem' },
            side_position: 'left', lead_height: { mobile: '17rem' }, card_style: 'elevated', text_align: 'center',
        } }, 'archive');
        assert.equal(prepared.thumbnail.height, '13.5rem');
        assert.equal(prepared[key].show_excerpt, false);
        assert.equal(prepared[key].show_date, false);
        assert.equal(prepared[key].gap.desktop, '3rem');
        if (key === 'mosaic_classic') {
            assert.equal(prepared[key].side_count, 0);
            assert.equal(prepared[key].side_position, 'left');
            assert.equal(prepared[key].lead_height.mobile, '17rem');
        } else {
            assert.equal(prepared[key].card_style, 'elevated');
            assert.equal(prepared[key].text_align, 'center');
        }
        const session = {
            surface: 'archive', activeTemplateKey: template,
            optionsModal: { key: template, surface: 'archive', value: prepared },
            draft: { archive_template_options: {} }, cloneOptions: methods.cloneOptions,
            previewBaseUrl: '/manage_article/templates/preview/__SURFACE__/__TEMPLATE__',
            buildPreviewUrl: methods.buildPreviewUrl, activeTemplateOptions: () => prepared,
            activeThemeColor: () => '#1AA879', rebuildPreview: methods.rebuildPreview, closeTemplateOptions() {},
        };
        methods.applyTemplateOptions.call(session);
        const url = new URL(session.previewUrl, 'https://example.test');
        assert.equal(JSON.parse(url.searchParams.get('template_options'))[key].gap.desktop, '3rem');
        assert.equal(url.searchParams.get('theme_color'), '#1AA879');
        assert.equal(session.draft.archive_template_options[template][key].show_date, false);
        assert.ok(computed.optionSections.call(session).some(section => section.key === template));
        assert.ok(!computed.optionSections.call({ ...session, surface: 'detail' }).some(section => section.key === template));
    });
}
