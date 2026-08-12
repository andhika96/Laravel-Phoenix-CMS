import assert from 'node:assert/strict';
import { existsSync } from 'node:fs';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';

globalThis.window ??= globalThis;
globalThis.window.matchMedia ??= () => ({ matches: false, addEventListener() {}, removeEventListener() {} });

const rootDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');

async function source(relativePath) {
    return readFile(join(rootDir, relativePath), 'utf8');
}

async function loadSfc(relativePath) {
    const filename = join(rootDir, relativePath);
    const contents = await readFile(filename, 'utf8');
    const { descriptor, errors } = parse(contents, { filename });
    assert.deepEqual(errors, []);
    const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
    component.render = Function('Vue', compile(descriptor.template.content, { mode: 'function' }).code)(Vue);
    return component;
}

function editorFor(settingsTab) {
    const EmptyControl = { template: '<div></div>' };
    return {
        settingsTab,
        responsiveDevice: 'desktop',
        responsiveDevices: [],
        widgetAdvancedControls: EmptyControl,
        linkControl: EmptyControl,
        typographyControl: { template: '<div>Typography</div>' },
        textStrokeControl: EmptyControl,
        textShadowControl: EmptyControl,
        fontFamilies: [],
        chooseMedia() {},
        openProIconLibrary() {},
        chooseProIconSvg() {},
        setResponsiveDevice() {},
        openControlResponsiveMenu() {},
        applyResponsiveDevice() {},
        responsiveDeviceLabel: () => 'Desktop',
        responsiveDeviceIcon: () => 'fas fa-desktop',
        isControlResponsiveMenuOpen: () => false,
        deviceOptionLabel: () => '',
        activeResponsiveKey: (key) => key,
        setResponsiveSetting(target, key, value) { target[key] = value; },
        sizeControlDisplayValue: (node, key, fallback) => Number.parseFloat(node.settings[key] || fallback) || 0,
        sizeControlUnit: (node, key, fallback) => String(node.settings[key] || fallback).match(/[a-z%]+$/i)?.[0] || 'px',
        onSizeControlInput() {},
        setSizeControlUnit() {},
        fontAwesomeStyleLabel: () => 'Brands',
    };
}

const blockquoteSettings = {
    skin: 'boxed',
    alignment: 'center',
    content: 'A useful quote',
    author: 'Ada Lovelace',
    tweetButton: true,
    tweetView: 'icon_text',
    tweetSkin: 'bubble',
    tweetLabel: 'Tweet',
    tweetUsername: 'ada',
    tweetTarget: 'custom',
    tweetUrl: 'https://example.com/article',
    contentGap: '18px',
    contentColor: '#344054',
    authorColor: '#101828',
    tweetSize: '14px',
    tweetBorderRadius: '8px',
    tweetColorMode: 'custom',
    tweetPrimaryColor: '#111827',
    tweetSecondaryColor: '#ffffff',
    tweetPrimaryColorHover: '#6979f8',
    tweetSecondaryColorHover: '#ffffff',
    borderColor: '#6979f8',
    borderWidth: '3px',
    borderGap: '16px',
    borderVerticalPadding: '8px',
    quoteColor: '#6979f8',
    quoteSize: '48px',
    quoteGap: '12px',
    boxPaddingTop: '24px',
    boxPaddingRight: '24px',
    boxPaddingBottom: '24px',
    boxPaddingLeft: '24px',
    boxBackground: '#f8fafc',
    boxBackgroundHover: '#eef2ff',
    boxBorderType: 'solid',
    boxBorderWidth: '1px',
    boxBorderWidthHover: '1px',
    boxBorderRadius: '8px',
    boxBorderRadiusHover: '12px',
    boxShadow: '0 4px 12px #10182814',
    boxShadowHover: '0 8px 24px #10182824',
};

const shareSettings = {
    items: [
        { id: 'facebook', network: 'facebook', customLabel: 'Facebook' },
        { id: 'x', network: 'x', customLabel: 'X' },
        { id: 'threads', network: 'threads', customLabel: 'Threads' },
    ],
    view: 'icon_text',
    showLabel: true,
    skin: 'gradient',
    shape: 'rounded',
    columns: '3',
    alignment: 'center',
    targetUrl: 'custom',
    customUrl: 'https://example.com/article',
    columnsGap: '12px',
    rowsGap: '10px',
    buttonSize: '44px',
    iconSize: '17px',
    buttonHeight: '44px',
    colorMode: 'custom',
    primaryColor: '#111827',
    secondaryColor: '#ffffff',
    primaryColorHover: '#6979f8',
    secondaryColorHover: '#ffffff',
};

test('Blockquote and Share Buttons definitions expose Pro defaults and strict normalization', async () => {
    const registrySource = await source('public/js/pagebuilder_elementor_v23/widget-registry.js');
    const context = { window: {} };
    vm.runInNewContext(registrySource, context);
    const definitionPaths = [
        'public/js/pagebuilder_elementor_v23/widgets/pro/blockquote/definition.js',
        'public/js/pagebuilder_elementor_v23/widgets/pro/share-buttons/definition.js',
    ];
    for (const path of definitionPaths) {
        assert.equal(existsSync(join(rootDir, path)), true, path + ' must exist');
        vm.runInNewContext(await source(path), context);
    }

    const blockquote = context.window.PageBuilderElementorV23Widgets.get('blockquote');
    const share = context.window.PageBuilderElementorV23Widgets.get('share_buttons');
    assert.equal(blockquote.category, 'pro');
    assert.equal(share.category, 'pro');
    assert.equal(blockquote.defaults().skin, 'border');
    assert.equal(blockquote.defaults().tweetButton, true);
    assert.equal(share.defaults().items.length >= 3, true);
    assert.equal(share.defaults().view, 'icon_text');

    const blockquoteNode = blockquote.normalize({ settings: { skin: 'invalid', tweetButton: 'false', tweetTarget: 'unsafe', borderWidthTop: 'expression(1)' } });
    assert.equal(blockquoteNode.settings.skin, 'border');
    assert.equal(blockquoteNode.settings.tweetButton, false);
    assert.equal(blockquoteNode.settings.tweetTarget, 'current');
    assert.equal(blockquoteNode.settings.borderWidthTop, '3px');

    const shareNode = share.normalize({ settings: {
        items: [{ id: 'x', network: 'x', customLabel: '<script>' }, { id: 'bad', network: 'unknown' }],
        view: 'invalid',
        columns: '99',
        targetUrl: 'javascript:alert(1)',
        columnsGap: 'expression(1)',
    } });
    assert.equal(shareNode.settings.view, 'icon_text');
    assert.equal(shareNode.settings.columns, 'auto');
    assert.equal(shareNode.settings.targetUrl, 'current');
    assert.equal(shareNode.settings.columnsGap, '8px');
    assert.equal(shareNode.settings.items.length, 1);
    assert.equal(shareNode.settings.items[0].customLabel, '<script>');
});

test('Blockquote and Share Buttons settings map Content, Style, and Advanced controls', async () => {
    const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue');
    const node = { type: 'blockquote', settings: blockquoteSettings };
    const blockquoteContent = await renderToString(Vue.createSSRApp(component, { node, editor: editorFor('content') }));
    for (const label of ['Skin', 'Alignment', 'Content', 'Author', 'Tweet Button', 'View', 'Tweet Button Skin', 'Label', 'Username', 'Target URL', 'Custom URL']) {
        assert.match(blockquoteContent, new RegExp(label.replace(/[()]/g, '\\$&')));
    }
    const blockquoteStyle = await renderToString(Vue.createSSRApp(component, { node, editor: editorFor('style') }));
    for (const label of ['Content', 'Text Color', 'Gap', 'Author', 'Tweet Button', 'Size', 'Border Color', 'Padding', 'Background Color', 'Box Shadow', 'Typography', 'Transition Duration']) {
        assert.match(blockquoteStyle, new RegExp(label));
    }
    const quotationStyle = await renderToString(Vue.createSSRApp(component, { node: { type: 'blockquote', settings: { ...blockquoteSettings, skin: 'quotation' } }, editor: editorFor('style') }));
    for (const label of ['Quote Color', 'Quote Size', 'Gap']) assert.match(quotationStyle, new RegExp(label));
    assert.match(await renderToString(Vue.createSSRApp(component, { node, editor: editorFor('advanced') })), /Advanced/);

    const shareNode = { type: 'share_buttons', settings: shareSettings };
    const shareContent = await renderToString(Vue.createSSRApp(component, { node: shareNode, editor: editorFor('content') }));
    for (const label of ['Network', 'Custom Label', 'View', 'Label', 'Skin', 'Shape', 'Columns', 'Alignment', 'Target URL', 'Custom URL']) {
        assert.match(shareContent, new RegExp(label.replace(/[()]/g, '\\$&')));
    }
    const shareStyle = await renderToString(Vue.createSSRApp(component, { node: shareNode, editor: editorFor('style') }));
    for (const label of ['Columns Gap', 'Rows Gap', 'Button Size', 'Icon Size', 'Button Height', 'Color', 'Primary Color', 'Secondary Color', 'Typography']) {
        assert.match(shareStyle, new RegExp(label));
    }
});

test('Blockquote and Share Buttons canvas expose safe content, responsive style, hover, and action hooks', async () => {
    const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
    const blockquoteHtml = await renderToString(Vue.createSSRApp(component, {
        item: { id: 'blockquote-test', type: 'blockquote', settings: { ...blockquoteSettings, content: '<script>alert(1)</script>' } },
        responsiveDevice: 'desktop',
    }));
    assert.match(blockquoteHtml, /data-blockquote/);
    assert.match(blockquoteHtml, /skin-boxed/);
    assert.match(blockquoteHtml, /data-blockquote-tweet/);
    assert.match(blockquoteHtml, /&lt;script&gt;alert\(1\)&lt;\/script&gt;/);
    assert.doesNotMatch(blockquoteHtml, /<script>alert\(1\)<\/script>/);

    const shareHtml = await renderToString(Vue.createSSRApp(component, {
        item: { id: 'share-test', type: 'share_buttons', settings: shareSettings },
        responsiveDevice: 'desktop',
    }));
    assert.match(shareHtml, /data-share-buttons/);
    assert.match(shareHtml, /data-share-network="x"/);
    assert.match(shareHtml, /data-share-network="threads"/);
    assert.match(shareHtml, /data-share-url/);
    assert.match(shareHtml, /columns-3/);
    assert.match(shareHtml, /target="_blank"/);
});

test('Blockquote and Share Buttons are wired through registry, app, Blade, and runtime', async () => {
    const config = await source('config/pagebuilder_elementor_v23_widgets.php');
    const app = await source('public/js/pagebuilder_elementor_v23/app.js');
    const blade = await source('resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php');
    const runtime = await source('public/js/pagebuilder_elementor_v23/frontend-runtime.js');
    for (const [type, label, icon] of [
        ['blockquote', 'Blockquote', 'fas fa-quote-left'],
        ['share_buttons', 'Share Buttons', 'fas fa-share-alt'],
    ]) {
        assert.match(config, new RegExp("'" + type + "'\\s*=>[\\s\\S]*?'category'\\s*=>\\s*'pro'"));
        assert.match(app, new RegExp(type + ":\\s*['\"]" + label + "['\"]"));
        assert.match(app, new RegExp(type + ":\\s*['\"]" + icon + "['\"]"));
        assert.match(blade, new RegExp("@case\\('" + type + "'\\)"));
    }
    assert.match(app, /blockquote.*share_buttons.*includes\(this\.node\.type\)/s);
    assert.match(runtime, /initProShareButtons/);
    assert.match(runtime, /navigator\.clipboard\.writeText/);
});
