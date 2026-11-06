import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const phase=process.argv[2]||'after';
const artifact=path.join(process.cwd(),'project-artifacts/midnight-corner-fix-20261106',phase);
const browser=await chromium.launch({channel:'chrome',headless:true});
const results=[];
try{
 const page=await browser.newPage();
 for(const viewport of [320,820,1280]){
  const data=await fs.readFile(path.join(artifact,`panel-${viewport}.png`));
  const checks=await page.evaluate(async dataUrl=>{
   const img=new Image();img.src=dataUrl;await img.decode();
   const canvas=document.createElement('canvas');canvas.width=img.width;canvas.height=img.height;
   const context=canvas.getContext('2d');context.drawImage(img,0,0);
   const points=[['upper_outer_curve',img.width-62,4,[6,11,8]],['lower_outer_curve',img.width-6,48,[6,11,8]],['right_edge_below_notch',img.width-6,76,[35,129,91]],['left_of_inner_curve',img.width-72,28,[35,129,91]]];
   return points.map(([point,x,y,expected])=>{const actual=Array.from(context.getImageData(x,y,1,1).data).slice(0,3);return{point,actual,expected,passed:actual.every((v,i)=>Math.abs(v-expected[i])<=2)};});
  },`data:image/png;base64,${data.toString('base64')}`);
  results.push(...checks.map(check=>({viewport,...check})));
 }
 await fs.writeFile(path.join(artifact,'pixel-checks.json'),JSON.stringify(results,null,2));
 const failures=results.filter(result=>!result.passed);
 console.log(`${results.length-failures.length}/${results.length} corner contour checks passed (${phase}).`);
 assert.equal(failures.length,0,JSON.stringify(failures));
}finally{await browser.close();}
