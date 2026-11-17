import fs from 'node:fs/promises';
import {chromium} from 'playwright-core';
import crypto from 'node:crypto';
const dir='project-artifacts/shared-css-auth-fixes-20261117';
const browser=await chromium.launch({channel:'chrome',headless:true});
const page=await browser.newPage({viewport:{width:1440,height:900}});
let original=true;
const sources=Object.fromEntries(await Promise.all(['extending-css-bootstrap-5.css','phoenix-cms.css','theme-responsive-typography.css'].map(async n=>[n,await fs.readFile(`${dir}/retired-sources/${n}`,'utf8')])));
await page.route('**/*',r=>{const n=new URL(r.request().url()).pathname.split('/').pop();return original&&sources[n]?r.fulfill({contentType:'text/css',body:sources[n]}):r.continue();});
const results=[];
for(const before of [true,false,true,false]){
 original=before;
 await page.goto('http://laravel-13-phoenix.aruna/__shared_css_qa_20261117/auth-arunika_lucent.html');
 await page.evaluate(()=>document.fonts.ready);
 await page.screenshot({path:`${dir}/auto-margin-${before?'old':'new'}.png`});
 const r=await page.evaluate(()=>{const e=document.querySelector('main'),s=getComputedStyle(e);return{margin:s.margin,left:s.marginLeft,right:s.marginRight,inline:s.marginInline,rect:e.getBoundingClientRect().toJSON(),sheets:[...document.styleSheets].map(s=>({href:s.href,css:[...s.cssRules].map(r=>r.cssText).join('\n')}))};});
 r.sheets=r.sheets.map(s=>({href:s.href,hash:crypto.createHash('sha256').update(s.css).digest('hex')}));results.push({before,...r});
}
await fs.writeFile(`${dir}/auto-margin-debug.json`,JSON.stringify(results,null,2));
console.log(results.map(({sheets,...r})=>r));
console.log('CSSOM identical:',JSON.stringify(results[0].sheets)===JSON.stringify(results[1].sheets));
await browser.close();
