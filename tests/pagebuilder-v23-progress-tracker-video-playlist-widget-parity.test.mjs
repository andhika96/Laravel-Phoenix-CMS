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
        fontAwesomeStyleLabel: () => 'Solid',
    };
}

const progressSettings = {
    trackerType: 'horizontal',
    relativeTo: 'selector',
    selector: '.article-content',
    direction: 'center',
    showPercentage: true,
    trackerSize: '8px',
    circleSize: '160px',
    indicatorColor: '#6979f8',
    indicatorWidth: '8px',
    indicatorAlignment: 'center',
    backgroundColor: '#e4e7ec',
    backgroundWidth: '8px',
    percentageColor: '#101828',
    progressTrackerPercentageFontSize: '14px',
    progressTrackerPercentageFontWeight: '600',
    progressTrackerPercentageLineHeight: '1.2em',
    progressTrackerPercentageTextShadow: 'none',
};

const playlistSettings = {
    playlistName: 'Course Playlist',
    playlistTitleTag: 'h3',
    items: [
        {
            id: 'video-1',
            type: 'youtube',
            link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            title: 'Getting Started',
            titleTag: 'h4',
            duration: '0:16',
            thumbnailUrl: '',
            showContentTabs: true,
            contentTabOneTitle: 'Overview',
            contentTabOneContent: 'Course overview',
            contentTabTwoTitle: 'Notes',
            contentTabTwoContent: 'Course notes',
        },
        { id: 'section-1', type: 'section', title: 'Advanced lessons', sectionContent: 'Advanced lessons' },
    ],
    tabsCollapsible: true,
    readMoreLabel: 'Read More',
    readLessLabel: 'Read Less',
    tabsHeight: '120px',
    imageOverlay: true,
    overlayImageUrl: '/poster.jpg',
    imageResolution: 'full',
    autoplayOnLoad: false,
    autoplayNext: true,
    indicateWatched: true,
    showVideoCount: true,
    showDuration: true,
    showThumbnails: true,
    dropdownAlignment: 'right',
    playIconSource: 'library',
    playIconClass: 'fas fa-play',
    playedIconSource: 'library',
    playedIconClass: 'fas fa-check',
    videoPosition: 'right',
    videoHeight: '360px',
    playlistNameBackground: '#101828',
    playlistNameColor: '#ffffff',
    videoCountColor: '#667085',
    itemBackground: '#ffffff',
    itemBackgroundHover: '#f2f4f7',
    itemColor: '#344054',
    itemColorHover: '#101828',
    durationColor: '#667085',
    iconColor: '#6979f8',
    iconShadow: '0 2px 6px rgba(16,24,40,.16)',
    iconSize: '18px',
    sectionBackgroundType: 'classic',
    sectionBackground: '#f8fafc',
    sectionBorderType: 'solid',
    sectionBorderColor: '#e4e7ec',
    sectionBorderWidth: '1px',
    sectionRadius: '6px',
    sectionBoxShadow: 'none',
    sectionPadding: '12px',
    tabsBorderWidth: '1px',
    tabsBorderColor: '#e4e7ec',
    tabsBackground: '#ffffff',
    tabsTitleColor: '#667085',
    tabsTitleActiveColor: '#6979f8',
    tabsContentColor: '#344054',
    tabsContentPadding: '14px',
    showMoreColor: '#6979f8',
    showMoreColorHover: '#5868e8',
};

test('Progress Tracker and Video Playlist definitions expose Pro defaults and normalization', async () => {
    const registrySource = await source('public/js/pagebuilder_elementor_v23/widget-registry.js');
    const context = { window: {} };
    vm.runInNewContext(registrySource, context);

    const definitionPaths = [
        'public/js/pagebuilder_elementor_v23/widgets/pro/progress-tracker/definition.js',
        'public/js/pagebuilder_elementor_v23/widgets/pro/video-playlist/definition.js',
    ];
    for (const path of definitionPaths) {
        assert.equal(existsSync(join(rootDir, path)), true, path + ' must exist');
        vm.runInNewContext(await source(path), context);
    }

    const progress = context.window.PageBuilderElementorV23Widgets.get('progress_tracker');
    const playlist = context.window.PageBuilderElementorV23Widgets.get('video_playlist');
    assert.equal(progress.category, 'pro');
    assert.equal(playlist.category, 'pro');
    assert.equal(progress.defaults().trackerType, 'horizontal');
    assert.equal(progress.defaults().relativeTo, 'page');
    assert.equal(progress.defaults().showPercentage, true);
    assert.equal(playlist.defaults().items.length, 3);
    assert.equal(playlist.defaults().items[0].type, 'youtube');
    assert.equal(playlist.defaults().items[0].titleTag, 'h4');
    assert.equal(playlist.defaults().items[0].duration, '0:16');
    assert.equal(playlist.defaults().items[0].showContentTabs, false);

    const normalizedProgress = progress.normalize({ settings: { trackerType: 'invalid', relativeTo: 'invalid', direction: 'invalid', trackerSize: 'expression(1)' } });
    assert.equal(normalizedProgress.settings.trackerType, 'horizontal');
    assert.equal(normalizedProgress.settings.relativeTo, 'page');
    assert.equal(normalizedProgress.settings.direction, 'left');
    assert.equal(normalizedProgress.settings.trackerSize, '6px');

    const normalizedPlaylist = playlist.normalize({ settings: { items: [{ type: 'invalid', titleTag: 'script', link: 'javascript:alert(1)' }], tabsHeight: 'expression(1)', dropdownAlignment: 'invalid' } });
    assert.equal(normalizedPlaylist.settings.items.length, 1);
    assert.equal(normalizedPlaylist.settings.items[0].type, 'youtube');
    assert.equal(normalizedPlaylist.settings.items[0].titleTag, 'h4');
    assert.equal(normalizedPlaylist.settings.items[0].link, '');
    assert.equal(normalizedPlaylist.settings.tabsHeight, '120px');
    assert.equal(normalizedPlaylist.settings.dropdownAlignment, 'right');
});

test('new Pro settings map Content, Style, responsive units, and Advanced controls', async () => {
    const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue');
    const progressContent = await renderToString(Vue.createSSRApp(component, { node: { type: 'progress_tracker', settings: progressSettings }, editor: editorFor('content') }));
    for (const label of ['Tracker Type', 'Progress Relative To', 'CSS Selector', 'Direction', 'Percentage']) assert.match(progressContent, new RegExp(label));
    const progressStyle = await renderToString(Vue.createSSRApp(component, { node: { type: 'progress_tracker', settings: progressSettings }, editor: editorFor('style') }));
    for (const label of ['Tracker Size', 'Progress Indicator', 'Background Color', 'Indicator Width', 'Alignment', 'Tracker Background', 'Percentage Color', 'Text Shadow']) assert.match(progressStyle, new RegExp(label));

    const playlistContent = await renderToString(Vue.createSSRApp(component, { node: { type: 'video_playlist', settings: playlistSettings }, editor: editorFor('content') }));
    for (const label of ['Playlist Name', 'HTML Tag', 'Playlist Items', 'Type', 'Link', 'Get Video Data', 'Title', 'Duration', 'Thumbnail', 'Content Tabs', 'Tabs', 'Collapsible', 'Read More Label', 'Image Overlay', 'Autoplay On Load', 'Next Up', 'Indicate Watched', 'Video Count', 'Thumbnails', 'Play icon', 'Played icon']) assert.match(playlistContent, new RegExp(label));
    const playlistStyle = await renderToString(Vue.createSSRApp(component, { node: { type: 'video_playlist', settings: playlistSettings }, editor: editorFor('style') }));
    for (const label of ['Video Position', 'Height', 'Playlist Name', 'Video Count', 'Normal', 'Hover', 'Duration', 'Icon', 'Background type', 'Border type', 'Box Shadow', 'Padding', 'Tabs', 'Active Color', 'Show More']) assert.match(playlistStyle, new RegExp(label));

    assert.match(await renderToString(Vue.createSSRApp(component, { node: { type: 'progress_tracker', settings: progressSettings }, editor: editorFor('advanced') })), /Advanced/);
    assert.match(await renderToString(Vue.createSSRApp(component, { node: { type: 'video_playlist', settings: playlistSettings }, editor: editorFor('advanced') })), /Advanced/);
});

test('new Pro canvases render visible, safe, and interactive markup', async () => {
    const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
    const progressHtml = await renderToString(Vue.createSSRApp(component, { item: { id: 'progress-test', type: 'progress_tracker', settings: { ...progressSettings, trackerType: 'circular' } }, responsiveDevice: 'desktop' }));
    assert.match(progressHtml, /data-progress-tracker/);
    assert.match(progressHtml, /pb-pro-progress-tracker--circular/);
    assert.match(progressHtml, /<svg/);
    assert.match(progressHtml, /50%/);

    const playlistHtml = await renderToString(Vue.createSSRApp(component, { item: { id: 'playlist-test', type: 'video_playlist', settings: { ...playlistSettings, items: [{ ...playlistSettings.items[0], title: '<script>alert(1)<\/script>' }] } }, responsiveDevice: 'desktop' }));
    assert.match(playlistHtml, /data-video-playlist/);
    assert.match(playlistHtml, /pb-pro-video-playlist/);
    assert.match(playlistHtml, /Getting Started|script/);
    assert.match(playlistHtml, /data-playlist-index/);
    assert.match(playlistHtml, /data-playlist-player/);
    assert.match(playlistHtml, /iframe|placeholder/);
    assert.doesNotMatch(playlistHtml, /<script>alert\(1\)<\/script>/);
});

test('new widgets are wired through registry, labels, Blade renderer, and frontend runtime', async () => {
    const config = await source('config/pagebuilder_elementor_v23_widgets.php');
    const app = await source('public/js/pagebuilder_elementor_v23/app.js');
    const canvas = await source('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
    const blade = await source('resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php');
    const runtime = await source('public/js/pagebuilder_elementor_v23/frontend-runtime.js');

    for (const type of ['progress_tracker', 'video_playlist']) {
        assert.match(config, new RegExp("'" + type + "'\\s*=>[\\s\\S]*?'category'\\s*=>\\s*'pro'"));
    }
    assert.match(app, /progress_tracker:\s*['"]Progress Tracker['"]/);
    assert.match(app, /video_playlist:\s*['"]Video Playlist['"]/);
    assert.match(app, /progress_tracker:\s*['"]fas fa-tasks['"]/);
    assert.match(app, /video_playlist:\s*['"]fas fa-list['"]/);
    assert.match(canvas, /pb-pro-progress-tracker/);
    assert.match(canvas, /pb-pro-video-playlist/);
    assert.match(blade, /@case\('progress_tracker'\)/);
    assert.match(blade, /@case\('video_playlist'\)/);
    assert.match(blade, /data-progress-tracker/);
    assert.match(blade, /data-video-playlist/);
    assert.match(runtime, /initProProgressTracker/);
    assert.match(runtime, /initProVideoPlaylist/);
    assert.match(runtime, /data-progress-tracker/);
    assert.match(runtime, /data-video-playlist/);
});
