import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';

const app = await readFile(new URL('../src/App.vue', import.meta.url), 'utf8');
const settings = await readFile(new URL('../src/components/SettingsModal.vue', import.meta.url), 'utf8');
const uploads = await readFile(new URL('../src/components/UploadPanel.vue', import.meta.url), 'utf8');
const config = await readFile(new URL('../vite.config.js', import.meta.url), 'utf8');

assert.match(app, /StorageSidebar/);
assert.match(app, /activeStorage/);
assert.match(app, /webkitdirectory/);
assert.match(app, /dragenter/);
assert.match(settings, /Cloudflare R2/);
assert.match(settings, /Multipart chunk/);
assert.match(settings, /Resumable uploads/);
assert.match(settings, /Verify checksum/);
assert.match(uploads, /Pause all/);
assert.match(uploads, /Resume all/);
assert.match(config, /base:\s*['"]\.\/['"]/);

console.log('Static contract passed: Local/R2, bulk upload, drag/drop, resumable settings, and portable build are present.');
