import { readFile, readdir, writeFile } from 'node:fs/promises';
import { join, resolve } from 'node:path';

const root = resolve('resources/pagebuilder_elementor_v24/modules/widgets/pro');
const entries = await readdir(root, { recursive: true });
const files = entries
    .map((entry) => join(root, String(entry)))
    .filter((file) => file.endsWith('Canvas.vue') || file.endsWith('frontend.blade.php'));
const before = String.raw`^-?\d+(?:\.\d+)?(?:px|em|rem)\s+-?\d+(?:\.\d+)?(?:px|em|rem)(?:\s+\d+(?:\.\d+)?(?:px|em|rem)){0,2}\s+`;
const after = String.raw`^(?:0|-?\d+(?:\.\d+)?(?:px|em|rem))\s+(?:0|-?\d+(?:\.\d+)?(?:px|em|rem))(?:\s+(?:0|\d+(?:\.\d+)?(?:px|em|rem))){0,2}\s+`;
let changed = 0;

for (const file of files) {
    const source = await readFile(file, 'utf8');
    const matches = source.split(before).length - 1;
    if (matches === 0) continue;
    if (matches !== 1) throw new Error(`${file}: expected one shadow grammar, found ${matches}`);
    await writeFile(file, source.replace(before, after));
    changed += 1;
}

if (changed !== 36) throw new Error(`Expected 36 Pro module assets, changed ${changed}`);
console.log(`Updated ${changed} Pro Canvas/frontend shadow sanitizers.`);
