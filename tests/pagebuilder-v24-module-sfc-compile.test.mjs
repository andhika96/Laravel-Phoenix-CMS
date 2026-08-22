import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import { parse, compileTemplate } from '@vue/compiler-sfc';

const modulesRoot = path.resolve(import.meta.dirname, '..', 'resources', 'pagebuilder_elementor_v24', 'modules');

function manifests(directory) {
    return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const target = path.join(directory, entry.name);
        if (entry.isDirectory()) return manifests(target);
        return entry.name === 'module.json' ? [target] : [];
    });
}

for (const manifestPath of manifests(modulesRoot)) {
    const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
    const directory = path.dirname(manifestPath);

    for (const filename of ['Canvas.vue', 'Settings.vue']) {
        test(`${manifest.type} ${filename} parses and compiles`, () => {
            const source = fs.readFileSync(path.join(directory, filename), 'utf8');
            const parsed = parse(source, { filename });
            assert.deepEqual(parsed.errors, []);
            assert.ok(parsed.descriptor.template);
            const compiled = compileTemplate({
                id: `v24-${manifest.type}-${filename}`,
                filename,
                source: parsed.descriptor.template.content,
            });
            assert.deepEqual(compiled.errors, []);
        });
    }
}
