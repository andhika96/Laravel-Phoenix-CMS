import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
import crypto from 'node:crypto';
const artifact = path.resolve('project-artifacts/shared-css-auth-fixes-20261117');
const manifest = JSON.parse((await fs.readFile(path.join(artifact, 'backup-manifest.json'),'utf8')).replace(/^\uFEFF/,''));
const sections = [['extending-css-bootstrap-5.css','BOOTSTRAP UTILITIES'],['phoenix-cms.css','PHOENIX COMPONENTS'],['theme-responsive-typography.css','RESPONSIVE TYPOGRAPHY']];
const chunks = [Buffer.from(`/*
 * PHOENIX SHARED CSS - single editable source
 *
 * INDEX
 * 01  BOOTSTRAP UTILITIES     former extending-css-bootstrap-5.css
 * 02  PHOENIX COMPONENTS      former phoenix-cms.css
 * 03  RESPONSIVE TYPOGRAPHY   former theme-responsive-typography.css
 *
 * Keep START/END flags intact: SharedStylesheetController serves each section
 * through its existing asset URL, preserving selective loading and cascade order.
 * Edit CSS here; no build command is required. Do not load this complete file
 * into layouts that previously loaded only some sections.
 */\n\n`)];
for(const [name,section] of sections) {
 const file = path.resolve('public/assets/css/shared',name);
 const bytes = await fs.readFile(file);
 const entry = manifest.find(e=>path.resolve(e.source)===file);
 assert.equal(crypto.createHash('sha256').update(bytes).digest('hex').toUpperCase(),entry.sha256);
 chunks.push(Buffer.from(`/* START: ${section} */\n`),bytes,Buffer.from(`\n/* END: ${section} */\n\n`));
}
await fs.writeFile(path.resolve('public/assets/css/shared/phoenix-shared.css'),Buffer.concat(chunks),{flag:'wx'});
await fs.mkdir(path.join(artifact,'retired-sources'));
for(const [name] of sections) await fs.rename(path.resolve('public/assets/css/shared',name),path.join(artifact,'retired-sources',name));
console.log('Consolidated 3 exact CSS bodies into phoenix-shared.css; retired originals retained in artifacts.');
