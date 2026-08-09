import assert from 'node:assert/strict';
import test from 'node:test';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join, relative, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const v20Root = resolve(root, 'public/js/pagebuilder_elementor/widgets');
const v23Root = resolve(root, 'public/js/pagebuilder_elementor_v23/widgets');

function filesUnder(directory) {
    return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const path = join(directory, entry.name);
        if (entry.isDirectory()) return filesUnder(path);
        if (entry.name.includes('.bak')) return [];
        return [path];
    });
}

function moduleFiles(directory) {
    return filesUnder(directory)
        .filter((path) => /(?:definition\.js|Canvas\.vue|Settings\.vue)$/.test(path))
        .map((path) => relative(directory, path).replaceAll('\\', '/'))
        .sort();
}

test('v2.3 owns the same widget module tree as v2.0', () => {
    const v20Files = moduleFiles(v20Root);
    const v23Files = moduleFiles(v23Root);

    assert.deepEqual(v23Files, v20Files);
    assert.ok(v23Files.length > 0);
});

test('every v2.3 definition preserves v2.0 behavior with v2.3 module paths', () => {
    const definitions = moduleFiles(v20Root).filter((path) => path.endsWith('definition.js'));

    for (const relativePath of definitions) {
        const v20Path = join(v20Root, relativePath);
        const v23Path = join(v23Root, relativePath);
        assert.equal(existsSync(v23Path), true, relativePath);

        const v20 = readFileSync(v20Path, 'utf8');
        const v23 = readFileSync(v23Path, 'utf8');
        let normalizedV20 = v20;
        const normalizedV23 = v23
            .replaceAll('pagebuilder_elementor_v23', 'pagebuilder_elementor')
            .replaceAll('PageBuilderElementorV23', 'PageBuilderElementor');

        if (relativePath === 'layout/container/definition.js') {
            normalizedV20 = normalizedV20
                .replace("\tconst uid = () => 'c_' + Math.random().toString(36).slice(2, 9);\n", '')
                .replace("\t\t\tconst columns = Math.max(1, Math.min(12, Number(node.settings?.gridColumns || 3)));\n", '')
                .replace("\t\t\t\tcolumns: Array.from({ length: columns }, () => ({ id: uid(), children: [] })),\n", '');
        }

        assert.equal(normalizedV23, normalizedV20, relativePath);
        assert.match(v23, /\btype\s*:/, relativePath);
        assert.match(v23, /\bdefaults\s*(?::|\()/, relativePath);
        assert.match(v23, /\bnormalize\s*\(/, relativePath);
    }
});

test('active v2.3 widget modules contain no v2.0 asset path', () => {
    for (const path of filesUnder(v23Root).filter((path) => /\.(?:js|vue)$/.test(path))) {
        const source = readFileSync(path, 'utf8');
        assert.equal(source.includes('/js/pagebuilder_elementor/'), false, relative(v23Root, path));
    }
});
