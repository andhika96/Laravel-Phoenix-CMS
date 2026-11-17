import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
import crypto from 'node:crypto';
import {chromium} from 'playwright-core';
const dir = path.resolve('project-artifacts/shared-css-auth-fixes-20261117');
const phase = process.argv[2] || 'baseline';
const baseline = phase === 'baseline' ? null : JSON.parse(await fs.readFile(path.join(dir, 'consumer-baseline.json'), 'utf8'));
const browser = await chromium.launch({channel:'chrome',headless:true});
const results = [], errors = [];
try {
 const page = await browser.newPage({reducedMotion:'reduce'});
 await page.route('**/*', route => route.request().method() === 'GET' ? route.continue() : route.abort());
 page.on('pageerror', e => errors.push(e.message));
 page.on('response', r => {if(r.status() >= 400) errors.push(`${r.status()} ${r.url()}`);});
 for (const file of (await fs.readdir(path.join(dir,'rendered'))).filter(f => f.endsWith('.html')).sort()) {
  for (const width of [390,820,1440]) for (const mode of ['light','dark']) {
   await page.setViewportSize({width,height:900});
   const response = await page.goto(`http://laravel-13-phoenix.aruna/__shared_css_qa_20261117/${file}`, {waitUntil:'load'});
   assert.equal(response.status(),200);
   await page.addStyleTag({content:'*,*::before,*::after{transition:none!important;animation:none!important;caret-color:transparent!important}'});
   await page.evaluate(mode => {document.documentElement.dataset.bsTheme = mode; document.body.dataset.bsTheme = mode;}, mode);
   await page.evaluate(() => document.fonts.ready);
   const values = await page.evaluate(() => [...document.querySelectorAll('html,body,body *')].filter(e=>e.getBoundingClientRect().width>0).map(e=>{
    const r=e.getBoundingClientRect(),s=getComputedStyle(e);
    return [e.tagName,e.className,r.x,r.y,r.width,r.height,...['fontSize','fontFamily','fontWeight','lineHeight','color','backgroundColor','padding','margin','border','display','position','gap','opacity','transform'].map(p=>s[p])];
   }));
   const record = {file,width,mode,elements:values.length,hash:crypto.createHash('sha256').update(JSON.stringify(values)).digest('hex')};
   if(baseline) assert.deepEqual(record,baseline.results[results.length],`${file}/${width}/${mode}`);
   results.push(record);
  }
 }
 assert.deepEqual(errors,[]);
 console.log(`${phase}: ${results.length} layout/viewport/color comparisons passed; ${errors.length} browser/resource errors.`);
} finally {
 await fs.writeFile(path.join(dir,`consumer-${phase}.json`),JSON.stringify({results,errors},null,2));
 await browser.close();
}
