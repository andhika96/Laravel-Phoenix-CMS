import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import { parse, compileTemplate } from '@vue/compiler-sfc';

const root = path.resolve(import.meta.dirname, '..');
const proRoot = path.join(root, 'resources', 'pagebuilder_elementor_v24', 'modules', 'widgets', 'pro');
const modules = {
    form: ['form', 'data-pro-form'],
    product_lead_form: ['product-lead-form', 'data-product-lead-form'],
    slides: ['slides', 'pb-pro-slides'],
    animated_headline: ['animated-headline', 'pb-pro-headline'],
    hotspot: ['hotspot', 'pb-pro-hotspot'],
    price_list: ['price-list', 'pb-pro-price-list'],
    price_table: ['price-table', 'pb-pro-price-table'],
    call_to_action: ['call-to-action', 'pb-pro-cta'],
    countdown: ['countdown', 'pb-pro-countdown'],
    carousel: ['carousel', 'pb-pro-carousel'],
    reviews: ['reviews', 'pb-pro-reviews'],
    testimonial_carousel: ['testimonial-carousel', 'pb-pro-testimonial-carousel'],
    media_carousel: ['media-carousel', 'pb-pro-media-carousel'],
    flip_box: ['flip-box', 'pb-pro-flip-box'],
    code_highlight: ['code-highlight', 'pb-pro-code-highlight'],
    blockquote: ['blockquote', 'pb-pro-blockquote'],
    share_buttons: ['share-buttons', 'pb-pro-share-buttons'],
    progress_tracker: ['progress-tracker', 'pb-pro-progress-tracker'],
    video_playlist: ['video-playlist', 'pb-pro-video-playlist'],
};

for (const [type, [slug, canvasMarker]] of Object.entries(modules)) {
    test(`Pro module ${type} owns complete independent assets`, () => {
        const directory = path.join(proRoot, slug);
        const expectedFiles = ['module.json', 'definition.js', 'Canvas.vue', 'Settings.vue', 'frontend.blade.php'];
        for (const file of expectedFiles) {
            assert.ok(fs.existsSync(path.join(directory, file)), `${type} is missing ${file}`);
        }

        const manifest = JSON.parse(fs.readFileSync(path.join(directory, 'module.json'), 'utf8'));
        assert.equal(manifest.type, type);
        assert.equal(manifest.category, 'pro');
        for (const asset of ['definition', 'canvas', 'settings', 'view']) {
            assert.equal(typeof manifest.assets[asset], 'string', `${type} manifest must declare ${asset}`);
        }
        for (const [asset, relativePath] of Object.entries(manifest.assets)) {
            assert.ok(fs.existsSync(path.join(directory, relativePath)), `${type} manifest ${asset} asset must exist`);
        }

        const definition = fs.readFileSync(path.join(directory, 'definition.js'), 'utf8');
        assert.doesNotMatch(definition, /widgets\/pro\/shared\/(?:Canvas|Settings)\.vue/);

        for (const file of ['Canvas.vue', 'Settings.vue']) {
            const source = fs.readFileSync(path.join(directory, file), 'utf8');
            const parsed = parse(source, { filename: file });
            assert.deepEqual(parsed.errors, [], `${type} ${file} must parse as an SFC`);
            assert.ok(parsed.descriptor.template, `${type} ${file} must own a template`);
            const compiled = compileTemplate({
                id: `v24-${type}-${file}`,
                filename: file,
                source: parsed.descriptor.template.content,
            });
            assert.deepEqual(compiled.errors, [], `${type} ${file} template must compile`);
            assert.doesNotMatch(
                parsed.descriptor.template.content,
                /v-(?:if|else-if)="type\s*===\s*['"][a-z_]+['"]"/,
                `${type} ${file} must not retain a multi-widget template branch`,
            );
        }

        const canvas = fs.readFileSync(path.join(directory, 'Canvas.vue'), 'utf8');
        assert.match(canvas, new RegExp(canvasMarker));

        const frontend = fs.readFileSync(path.join(directory, 'frontend.blade.php'), 'utf8');
        assert.doesNotMatch(frontend, /@switch\s*\(\$type\)|@case\s*\(/);
        assert.match(frontend, new RegExp(canvasMarker));
    });
}
