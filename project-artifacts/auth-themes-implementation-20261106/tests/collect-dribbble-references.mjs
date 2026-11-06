import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
import path from 'node:path';
const artifact=path.join(process.cwd(),'project-artifacts/auth-themes-implementation-20261106/references');
await fs.mkdir(artifact,{recursive:true});
const browser=await chromium.launch({channel:'chrome',headless:true});
const results=[];
try {
 for(const [name,url] of [['clean','https://dribbble.com/shots/24750563-Clean-Login-Design'],['ocean','https://dribbble.com/shots/26236483-Login-Page'],['midnight','https://dribbble.com/shots/23511411-Sign-in-Page-design']]) {
  const page=await browser.newPage();
  await page.goto(url,{waitUntil:'domcontentloaded',timeout:25000});
  const images=await page.locator('img').evaluateAll(items=>items.map(i=>({alt:i.alt,src:i.currentSrc||i.src})).filter(i=>i.src.includes('userupload')));
  results.push({name,url,images,og:await page.locator('meta[property="og:image"]').getAttribute('content').catch(()=>null)});
  await page.close();
 }
} finally {await browser.close();await fs.writeFile(path.join(artifact,'dribbble-images.json'),JSON.stringify(results,null,2));}
console.log(JSON.stringify(results,null,2));
