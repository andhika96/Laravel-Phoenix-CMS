import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const artifact=path.join(process.cwd(),'project-artifacts/midnight-corner-fix-20261106');
const phase=process.argv[2]||'after';
await fs.mkdir(path.join(artifact,phase),{recursive:true});
const browser=await chromium.launch({channel:'chrome',headless:true});
const results=[];
try{
 const page=await browser.newPage({viewport:{width:1280,height:800},reducedMotion:'reduce'});
 await page.goto('http://laravel-13-phoenix.aruna/auth/login',{waitUntil:'networkidle'});
 await page.addStyleTag({path:path.join(process.cwd(),'public/assets/css/auth/auth-modern.css')});
 await page.evaluate(()=>{
  const host=document.createElement('main');host.id='cornerQa';host.className='ph-auth-modern-root';
  host.dataset.modernTheme='midnight_story';host.dataset.mobileVisual='below';
  host.style='position:fixed;inset:0;z-index:9999;--modern-surface:#060b08;--modern-panel-color:#23815b;--modern-bg:#080d0a;--modern-panel-text:#fff;';
  host.innerHTML='<div class="ph-auth-surface" style="display:block;height:440px;min-height:0;max-width:620px"><aside class="ph-auth-panel" style="height:380px;width:calc(100% - 44px)"><h2 class="ph-auth-panel-title">Stories from our community</h2></aside></div>';
  document.body.appendChild(host);
 });
 for(const width of [320,820,1280]){
  await page.setViewportSize({width,height:800});
  const panel=page.locator('#cornerQa .ph-auth-panel');
  await panel.screenshot({path:path.join(artifact,phase,`panel-${width}.png`),animations:'disabled'});
  const bounds=await panel.boundingBox();
  await page.screenshot({path:path.join(artifact,phase,`corner-${width}.png`),clip:{x:bounds.x+bounds.width-132,y:bounds.y-10,width:144,height:112},animations:'disabled'});
  const metrics=await panel.evaluate(e=>({width:e.getBoundingClientRect().width,height:e.getBoundingClientRect().height,background:getComputedStyle(e).backgroundColor,mask:getComputedStyle(e).maskImage,headingWidth:e.querySelector('h2').getBoundingClientRect().width}));
  assert.ok(metrics.width>132);
  results.push({viewport:width,...metrics});
 }
 await fs.writeFile(path.join(artifact,phase,'metrics.json'),JSON.stringify(results,null,2));
 console.log(`Captured ${results.length} viewport sizes (${phase}).`);
}finally{await browser.close();}
