import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const app = readFileSync('public/js/pagebuilder_elementor_v24/app.js', 'utf8');
const css = readFileSync('public/assets/css/pagebuilder_elementor_v24.css', 'utf8');
const shell = readFileSync('resources/views/pagebuilder_elementor_v24/editor_shell.blade.php', 'utf8');

test('automatic compiled native review exposes evidence and explicit correction state', () => {
    assert.match(app, /automaticCompiledNativeState = ref\(/);
    assert.match(app, /automaticCompiledNativeSections = computed\(/);
    assert.match(app, /function overrideAutomaticLayout\(sectionId, viewport, patch\)/);
    assert.match(app, /rule: 'user\.override'/);
    assert.match(app, /source preview/);
    assert.match(app, /Decision evidence/);
    assert.match(app, /Continue to widget mapping/);
});

test('automatic compiled native review does not expose a recommendation selector or exact visual fallback', () => {
    assert.doesNotMatch(app, /Use recommendations/);
    assert.doesNotMatch(app, /Exact Visual/);
    assert.match(app, /sandbox=""/);
    assert.match(app, /automaticCompiledNativeAnalyzeUrl/);
    assert.match(shell, /automaticCompiledNativeAnalyzeUrl:/);
});

test('automatic compiled native review has responsive no-horizontal-overflow surfaces', () => {
    assert.match(css, /\.pb-automatic-compiled-native-body\s*\{[\s\S]*grid-template-columns:/);
    assert.match(css, /\.pb-automatic-compiled-native-iframe-shell\s*\{[\s\S]*overflow:\s*hidden;/);
    assert.match(css, /@media \(max-width: 720px\)/);
});

console.log('Automatic Compiled Native review UI contract passed.');
