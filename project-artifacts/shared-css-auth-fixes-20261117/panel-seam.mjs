import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
import {chromium} from 'playwright-core';
const dir=path.resolve('project-artifacts/shared-css-auth-fixes-20261117'),phase=process.argv[2]||'before';
const browser=await chromium.launch({channel:'chrome',headless:true}),results=[];
try {
 for(const dpr of [1,1.25,1.35,1.5,2])for(const width of [820,1280,1422]) {
  const context=await browser.newContext({deviceScaleFactor:dpr,viewport:{width,height:900}}),page=await context.newPage();
  await page.goto('http://laravel-13-phoenix.aruna/__auth_fix_qa_20261117/midnight_story-login.html');
  await page.addStyleTag({content:'.ph-auth-panel>*{visibility:hidden!important}'});
  const panel=page.locator('.ph-auth-panel');
  const rect=await panel.boundingBox();
  const buffer=await panel.screenshot();
  const pixels=await page.evaluate(async({data,width,dpr})=>{
   const img=new Image();img.src=data;await img.decode();const c=document.createElement('canvas');c.width=img.width;c.height=img.height;const cx=c.getContext('2d');cx.drawImage(img,0,0);
   const reference=Array.from(cx.getImageData(Math.round(30*dpr),Math.round(120*dpr),1,1).data).slice(0,3);
   const samples=[];
   for(const y of [90,120,160])for(let offset=-2;offset<=2;offset++){
    const x=Math.round((width-99)*dpr)+offset,rgb=Array.from(cx.getImageData(x,Math.round(y*dpr),1,1).data).slice(0,3);
    samples.push({x,y:Math.round(y*dpr),rgb,delta:Math.max(...rgb.map((v,i)=>Math.abs(v-reference[i])))});
   }
   return{reference,samples};
  },{data:`data:image/png;base64,${buffer.toString('base64')}`,width:rect.width,dpr});
  const worst=Math.max(...pixels.samples.map(p=>p.delta));
  results.push({width,dpr,panelWidth:rect.width,worst,...pixels});
  if(dpr===1.35&&width===1422)await fs.writeFile(path.join(dir,`panel-${phase}.png`),buffer);
  await context.close();
 }
 console.log(`${phase}: ${results.filter(r=>r.worst>2).length}/${results.length} seam cases show visible color gaps.`);
 if(phase!=='before')assert.ok(results.every(r=>r.worst<=2),'Mask seam persists; inspect panel-seam results.');
}finally{await fs.writeFile(path.join(dir,`panel-seam-${phase}.json`),JSON.stringify(results,null,2));await browser.close();}
