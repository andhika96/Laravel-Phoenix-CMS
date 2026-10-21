const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const source = fs.readFileSync('resources/views/manage_article/templates/preview.blade.php', 'utf8');
const script = [...source.matchAll(/<script>([\s\S]*?)<\/script>/g)].at(-1)[1];
const listeners = new Map();
vm.runInNewContext(script, {document: {addEventListener: (type, fn) => listeners.set(type, fn)}});
for (const href of ['#article-section-1', '/article/example', 'https://example.com', null]) {
    let prevented = false;
    listeners.get('click')({target: {closest: () => ({getAttribute: () => href})}, preventDefault: () => {prevented = true;}});
    assert.equal(prevented, !href?.startsWith('#'));
}
console.log('4 fixture-link guard cases passed: local TOC allowed; external navigation blocked.');
