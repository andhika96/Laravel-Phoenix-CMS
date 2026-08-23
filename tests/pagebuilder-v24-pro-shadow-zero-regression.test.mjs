import assert from 'node:assert/strict';
import { readFileSync, readdirSync } from 'node:fs';
import { join, resolve } from 'node:path';
import test from 'node:test';
import { parse } from '@vue/compiler-sfc';

globalThis.window ??= globalThis;
globalThis.window.matchMedia ??= () => ({ matches: false, addEventListener() {}, removeEventListener() {} });

const proRoot = resolve('resources/pagebuilder_elementor_v24/modules/widgets/pro');
const files = readdirSync(proRoot, { recursive: true }).map((entry) => join(proRoot, String(entry)));
const canvases = files.filter((file) => file.endsWith('Canvas.vue') && readFileSync(file, 'utf8').includes('safeTextShadow(value'));
const frontends = files.filter((file) => file.endsWith('frontend.blade.php') && readFileSync(file, 'utf8').includes('$safeShadow = function'));

test('every Pro Canvas accepts unitless zero in otherwise unit-qualified CSS shadows', () => {
    assert.equal(canvases.length, 19);
    for (const file of canvases) {
        const source = readFileSync(file, 'utf8');
        const { descriptor, errors } = parse(source, { filename: file });
        assert.deepEqual(errors, [], file);
        const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
        assert.equal(
            component.methods.safeTextShadow('0 2px 6px rgba(16,24,40,.16)', 'none'),
            '0 2px 6px rgba(16,24,40,.16)',
            file,
        );
    }
});

test('every Pro frontend sanitizer accepts the same unitless-zero shadow grammar', () => {
    assert.equal(frontends.length, 19);
    for (const file of frontends) {
        const source = readFileSync(file, 'utf8');
        assert.match(source, /\^\(\?:0\|-\?\\d\+/, file);
    }
});
