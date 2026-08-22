import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';

globalThis.window ??= globalThis;

const rootDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');

async function loadCanvas() {
    const filename = join(rootDir, 'resources/pagebuilder_elementor_v24/modules/widgets/general/image-box/Canvas.vue');
    const contents = await readFile(filename, 'utf8');
    const { descriptor, errors } = parse(contents, { filename });
    assert.deepEqual(errors, []);
    const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
    component.render = Function('Vue', compile(descriptor.template.content, { mode: 'function', prefixIdentifiers: true }).code)(Vue);
    return component;
}

async function renderCanvas(component, device) {
    const warnings = [];
    const app = Vue.createSSRApp(component, {
        item: {
            id: `image-box-${device}`,
            type: 'image_box',
            settings: {
                imageUrl: '/vehicle.jpg',
                imageAlt: 'Vehicle',
                title: 'Vehicle title',
                description: 'Vehicle description',
                imagePosition: 'left',
                imagePositionTablet: 'top',
                imagePositionMobile: 'right',
                imageWidth: '40%',
                imageWidthTablet: '60%',
                imageWidthMobile: '50%',
                alignment: 'left',
                imageSpacing: '15px',
            },
        },
        responsiveDevice: device,
    });
    app.config.warnHandler = (message) => warnings.push(message);
    const html = await renderToString(app);
    assert.deepEqual(warnings, [], warnings.join('\n'));
    return html;
}

test('Image Box horizontal media owns configured width while its image is constrained to the track', async () => {
    const component = await loadCanvas();
    const desktop = await renderCanvas(component, 'desktop');

    assert.match(desktop, /pb-image-box--position-left/);
    assert.match(desktop, /pb-image-box__media" style="[^"]*width:40%;[^"]*flex:0 0 40%/);
    assert.match(desktop, /pb-image-box__image"[^>]*style="width:100%;max-width:100%/);

    const mobile = await renderCanvas(component, 'mobile');
    assert.match(mobile, /pb-image-box--position-right/);
    assert.match(mobile, /pb-image-box__media" style="[^"]*width:50%;[^"]*flex:0 0 50%/);
    assert.match(mobile, /pb-image-box__image"[^>]*style="width:100%;max-width:100%/);
});

test('Image Box top position keeps a full media row and applies configured width to the image', async () => {
    const component = await loadCanvas();
    const tablet = await renderCanvas(component, 'tablet');

    assert.match(tablet, /pb-image-box--position-top/);
    assert.match(tablet, /pb-image-box__media" style="[^"]*width:100%;[^"]*flex:0 0 auto/);
    assert.match(tablet, /pb-image-box__image"[^>]*style="width:60%;max-width:100%/);
});
