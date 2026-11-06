import {chromium} from 'playwright-core';
import path from 'node:path';
import fs from 'node:fs/promises';
import assert from 'node:assert/strict';
const artifact=path.join(process.cwd(),'project-artifacts/auth-themes-implementation-20261106');
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
 const page=await browser.newPage({viewport:{width:1920,height:1000}});
 await page.goto('http://laravel-13-phoenix.aruna/auth/login',{waitUntil:'load'});
 await page.locator('[name="email"]').fill(process.env.PH_QA_ADMIN_EMAIL);
 await page.locator('[name="password"]').fill(process.env.PH_QA_ADMIN_PASSWORD);
 await page.locator('input[type="submit"]').click();
 await page.waitForURL(url=>!url.pathname.startsWith('/auth/'));
 await page.goto('http://laravel-13-phoenix.aruna/awesome_admin/appearance',{waitUntil:'networkidle'});
 const metrics=await page.evaluate(()=>{
  const grid=document.querySelector('.ph-auth-theme-grid'),tabs=document.querySelector('.ph-auth-page-tabs');
  const config=JSON.parse(document.getElementById('ph-auth-appearance-config').textContent);
  return {gridWidth:grid.clientWidth,parentWidth:grid.parentElement.clientWidth,columns:getComputedStyle(grid).gridTemplateColumns.split(' ').length,checkSize:document.querySelector('.ph-auth-theme-selected').offsetWidth,images:[...grid.querySelectorAll('img')].every(i=>i.complete&&i.naturalWidth>0),themesByPage:Object.fromEntries(Object.entries(config.themes).map(([p,t])=>[p,t.length])),clip:{x:grid.getBoundingClientRect().x,y:tabs.getBoundingClientRect().y-12,width:grid.getBoundingClientRect().width,height:grid.getBoundingClientRect().bottom-tabs.getBoundingClientRect().y+24}};
 });
 assert.equal(metrics.gridWidth,metrics.parentWidth);
 assert.equal(metrics.columns,5);
 assert.equal(metrics.checkSize,25);
 assert.ok(metrics.images);
 await page.screenshot({path:path.join(artifact,'screenshots/appearance-final-gallery.png'),clip:metrics.clip,animations:'disabled'});
 await page.locator('[data-theme="ocean_split"]').click();
 metrics.typography=await page.locator('.ph-auth-option-groups .accordion-button').first().evaluate(e=>({body:getComputedStyle(document.body).fontSize,title:getComputedStyle(e.querySelector('strong')).fontSize,help:getComputedStyle(e.querySelector('small')).fontSize}));
 assert.equal(metrics.typography.title,metrics.typography.body);
 metrics.colorisAvailable=await page.evaluate(()=>typeof Coloris==='function'&&document.querySelectorAll('.clr-field').length>0);
 assert.ok(metrics.colorisAvailable);
 await fs.writeFile(path.join(artifact,'final-appearance-metrics.json'),JSON.stringify(metrics,null,2));
 console.log(JSON.stringify(metrics));
} finally {await browser.close();}
