import assert from 'node:assert/strict';
import test from 'node:test';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join, relative, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const v20Root = resolve(root, 'public/js/pagebuilder_elementor/widgets');
const v24Root = resolve(root, 'resources/pagebuilder_elementor_v24/modules');

function filesUnder(directory) {
    return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const target = join(directory, entry.name);
        if (entry.isDirectory()) return filesUnder(target);
        if (entry.name.includes('.bak')) return [];
        return [target];
    });
}

function v20Types() {
    return filesUnder(v20Root)
        .filter((file) => file.endsWith('definition.js'))
        .map((file) => readFileSync(file, 'utf8').match(/registry\.register\(\s*\{[\s\S]{0,500}?\btype\s*:\s*['"]([a-z][a-z0-9_]*)['"]/)?.[1])
        .filter(Boolean);
}

function v24Modules() {
    return filesUnder(v24Root)
        .filter((file) => file.endsWith('module.json'))
        .map((file) => ({ directory: dirname(file), manifest: JSON.parse(readFileSync(file, 'utf8')) }))
        .sort((left, right) => left.manifest.type.localeCompare(right.manifest.type));
}

test('v2.4 owns every v2.0 widget type and may add isolated v2.4 modules', () => {
    const modules = v24Modules();
    const v24Types = modules.map(({ manifest }) => manifest.type);

    assert.deepEqual([...new Set(v20Types())].filter((type) => !v24Types.includes(type)), []);
    assert.equal(modules.length, 49);
    assert.equal(new Set(v24Types).size, 49);
});

test('every v2.4 definition owns a valid isolated module contract', () => {
    for (const { directory, manifest } of v24Modules()) {
		assert.deepEqual(
			['definition', 'canvas', 'settings', 'view'].filter((key) => !Object.hasOwn(manifest.assets, key)),
			[],
			manifest.type,
		);

		for (const filename of ['definition.js', 'Canvas.vue', 'Settings.vue', 'frontend.blade.php']) {
            assert.equal(existsSync(join(directory, filename)), true, `${manifest.type} ${filename}`);
		}
		for (const optional of ['runtime', 'styles']) {
			if (manifest.assets[optional]) assert.equal(existsSync(join(directory, manifest.assets[optional])), true, `${manifest.type} ${optional}`);
		}

        const definition = readFileSync(join(directory, 'definition.js'), 'utf8');
        assert.equal(definition.includes('/js/pagebuilder_elementor/'), false, manifest.type);
        assert.match(definition, /\btype\s*:/, manifest.type);
        assert.match(definition, /\bdefaults\s*(?::|\()/, manifest.type);
        assert.match(definition, /\bnormalize\s*(?::|\()/, manifest.type);
    }
});

test('active v2.4 widget modules contain no v2.0 asset path', () => {
    for (const file of filesUnder(v24Root).filter((file) => /\.(?:js|vue)$/.test(file))) {
        const source = readFileSync(file, 'utf8');
        assert.equal(source.includes('/js/pagebuilder_elementor/'), false, relative(v24Root, file));
    }
});
