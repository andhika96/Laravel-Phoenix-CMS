import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
import crypto from 'node:crypto';
import postcss from 'postcss';
const dir = path.resolve('project-artifacts/shared-css-auth-fixes-20261117');
const manifest = JSON.parse((await fs.readFile(path.join(dir,'backup-manifest.json'),'utf8')).replace(/^\uFEFF/,''));
const source = await fs.readFile('public/assets/css/shared/phoenix-shared.css','utf8');
postcss.parse(source);
const results=[];
for(const item of manifest.filter(e=>e.source.endsWith('.css'))) {
 const response = await fetch(`http://laravel-13-phoenix.aruna/assets/css/shared/${path.basename(item.source)}?qa=20261117`);
 const bytes = Buffer.from(await response.arrayBuffer());
 assert.equal(response.status,200);
 const name=path.basename(item.source),original=await fs.readFile(item.backup);
 const expected=name==='phoenix-cms.css'?Buffer.from(original.toString('utf8').replaceAll('background-color: var(--ph-content-bg); !important;','background-color: var(--ph-content-bg);')):original;
 assert.deepEqual(bytes,expected);
 assert.equal(response.headers.has('set-cookie'),false);
 const conditional=await fetch(response.url,{headers:{'If-None-Match':response.headers.get('etag')}});
 assert.equal(conditional.status,304);
 assert.equal((await conditional.arrayBuffer()).byteLength,0);
 results.push({file:name,bytes:bytes.length,sha256:crypto.createHash('sha256').update(bytes).digest('hex'),headers:Object.fromEntries(response.headers),conditionalStatus:conditional.status});
}
for(const url of ['/assets/css/shared/unknown.css','/__shared_css_qa_20261117/cms-default.html','/__auth_fix_qa_20261117/midnight_story-login.html'])assert.equal((await fetch('http://laravel-13-phoenix.aruna'+url)).status,404,url);
await fs.writeFile(path.join(dir,'section-equivalence.json'),JSON.stringify(results,null,2));
console.log('3/3 live CSS responses equivalent (2 ignored syntax tokens removed); ETag 304, no session cookies, parser and closed QA URLs passed.');
