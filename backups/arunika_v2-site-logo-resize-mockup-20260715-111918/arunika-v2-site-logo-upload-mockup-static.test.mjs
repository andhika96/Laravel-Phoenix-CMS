import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';

const mockupPath = 'public/mockups/site-logo-upload-config-mockup.html';

assert.ok(existsSync(mockupPath), 'the standalone Site Logo upload mockup should exist');

const mockup = readFileSync(mockupPath, 'utf8');

for (const id of [
    'siteNameInput',
    'logoFileInput',
    'logoDropzone',
    'chooseLogoButton',
    'useSampleLogo',
    'removeLogo',
    'saveMockup',
    'sidebarPreview',
    'expandedPreview',
    'collapsedPreview',
]) {
    assert.match(mockup, new RegExp(`id=["']${id}["']`), `mockup should expose #${id}`);
}

assert.match(
    mockup,
    /\/assets\/logos\/laraphoenix_onlybird_colored_2\.png/,
    'mockup should use the real LaraPhoenix logo asset as its sample',
);
assert.match(mockup, /accept=["']image\/png,image\/jpeg,image\/webp,image\/svg\+xml["']/, 'file input should limit visible choices to supported image formats');
assert.match(mockup, /2 \* 1024 \* 1024/, 'file validation should enforce the 2 MB limit');
assert.match(mockup, /FileReader/, 'the selected local logo should be previewed with FileReader');
assert.match(mockup, /dragover/, 'the upload zone should support drag and drop');
assert.match(mockup, /logoState\.src = ''/, 'removing a logo should clear the logo source');
assert.match(mockup, /prototype only/i, 'the page should be clearly labelled as a non-production prototype');

const inlineScripts = [...mockup.matchAll(/<script>([\s\S]*?)<\/script>/g)].map((match) => match[1]);
assert.ok(inlineScripts.length > 0, 'mockup should include its interactive script');
for (const script of inlineScripts) {
    assert.doesNotThrow(() => new Function(script), 'mockup inline JavaScript should parse');
}

console.log('Arunika v2 Site Logo upload mockup regression passed.');
