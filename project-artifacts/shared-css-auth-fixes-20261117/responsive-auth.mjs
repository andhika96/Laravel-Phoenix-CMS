import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
import {chromium} from 'playwright-core';
const dir=path.resolve('project-artifacts/shared-css-auth-fixes-20261117');
const browser=await chromium.launch({channel:'chrome',headless:true}),results=[],errors=[];
try {
 const page=await browser.newPage({reducedMotion:'reduce'});
 page.on('pageerror',e=>errors.push(e.message));
 await page.route('**/*',r=>r.request().method()==='GET'?r.continue():r.abort());
 for(const file of (await fs.readdir(path.join(dir,'auth-rendered'))).filter(f=>f.endsWith('.html')))for(const width of [320,1920]){
  await page.setViewportSize({width,height:900});
  await page.goto(`http://laravel-13-phoenix.aruna/__auth_fix_qa_20261117/${file}`,{waitUntil:'load'});
  await page.evaluate(()=>document.fonts.ready);
  assert.equal(await page.locator('input[type=submit]').count(),1);
  const metrics=await page.evaluate(()=>({document:document.documentElement.scrollWidth,viewport:innerWidth,controls:[...document.querySelectorAll('.form-control,input[type=submit]')].map(e=>{const r=e.getBoundingClientRect();return{x:r.x,right:r.right,width:r.width,height:r.height};})}));
  assert.ok(metrics.document<=width,`${file}/${width} document overflow`);
  assert.ok(metrics.controls.every(r=>r.x>=0&&r.right<=width+.5),`${file}/${width} control overflow`);
  results.push({file,width,...metrics});
  if(width===320&&file.startsWith('midnight_story-'))for(const position of ['above','below']){
   await page.locator('#ph-app-auth').evaluate((e,v)=>e.dataset.mobileVisual=v,position);
   const panel=await page.locator('.ph-auth-panel').boundingBox();
   assert.ok(panel&&panel.width>0&&panel.x>=0&&panel.x+panel.width<=width,`${file}/${position} panel overflow`);
   results.push({file,width,mobilePanel:position,panel});
  }
  if((file==='midnight_story-login.html'&&width===320)||(file==='ocean_split-login.html'&&width===1920))await page.screenshot({path:path.join(dir,`${file.replace('.html','')}-${width}-after.png`),fullPage:true});
 }
 assert.deepEqual(errors,[]);
 console.log(`${results.length} responsive checks passed: all 32 auth views at 320/1920 plus 8 Midnight mobile panel placements.`);
}finally{await fs.writeFile(path.join(dir,'responsive-auth.json'),JSON.stringify({results,errors},null,2));await browser.close();}
