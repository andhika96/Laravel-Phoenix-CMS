import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import test from 'node:test';

const root = resolve(import.meta.dirname, '..');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v24/app.js'), 'utf8');

const flexLayoutStyles = [
    resolve(root, 'resources/pagebuilder_elementor_v24/modules/layout/container/styles.css'),
    resolve(root, 'resources/pagebuilder_elementor_v24/modules/layout/container-fluid/styles.css'),
].map((path) => readFileSync(path, 'utf8'));

test('Flex resize handle is centered on the column boundary', () => {
    for (const css of flexLayoutStyles) {
        assert.match(
            css,
            /\.pb-container-edge-resizer\s*\{[\s\S]*?right:\s*calc\(var\(--pb-container-resizer-hit-size\)\s*\/\s*-2\);/,
            'the handle center must stay on the resizable column edge'
        );
        assert.doesNotMatch(
            css,
            /right:\s*calc\(\(var\(--pb-container-resizer-hit-size\)\s*\+\s*var\(--pb-flex-column-gap/,
            'the handle must not be offset into the flex gap'
        );
    }
});

test('Flex resize keeps pointer movement mapped to the adjacent width pair', () => {
    const resizeBody = app.match(/function startContainerEdgeResize\(event, parent, index\) \{([\s\S]*?)\n\s*const selectedNode =/)?.[1] || '';

    assert.match(resizeBody, /const delta = \(Number\(moveEvent\.clientX\) \|\| 0\) - startX/);
    assert.match(resizeBody, /const nextWidth = Math\.min\(Math\.max\(startWidth \+ delta, minPx\), pairWidth - minPx\)/);
    assert.match(resizeBody, /applyAdjacentContainerWidths\(children, index, requested, responsiveDevice\.value, \{ minPercent \}\)/);
});

test('layout nodes keep their Flex sizing branch instead of widget width styles', () => {
    const shellStyleBody = app.match(/nodeShellStyle\(\) \{([\s\S]*?)\n\s*contentShellStyle\(\)/)?.[1] || '';

    assert.match(
        shellStyleBody,
        /if \(this\.isWidgetNode && this\.hasNewGeneralAdvancedControls\) return widgetAdvancedPreviewStyle\(s, device\)/,
        'only widgets may use the shared widget width style'
    );
    assert.doesNotMatch(
        shellStyleBody,
        /if \(this\.hasSharedAdvancedControls\) return widgetAdvancedPreviewStyle\(s, device\)/,
        'layout Containers must reach their Flex sizing branch'
    );
});
