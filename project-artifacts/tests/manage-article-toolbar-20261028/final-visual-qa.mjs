import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const out=path.resolve('project-artifacts/qa/manage-article-toolbar-20261028');
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
  const page=await browser.newPage({ignoreHTTPSErrors:true,viewport:{width:1440,height:1000}}),cases=[];
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  await page.locator('#ph-app-manage-article tr[data-article-id]').first().waitFor();
  for(const [width,theme] of [[1440,'light'],[768,'light'],[390,'light'],[768,'dark'],[390,'dark']]) {
    await page.setViewportSize({width,height:1100});
    await page.goto('https://laravel-13-phoenix.aruna/manage_article');
    const app=page.locator('#ph-app-manage-article');
    await app.locator('tr[data-article-id]').first().waitFor();
    await page.evaluate(theme=>document.documentElement.setAttribute('data-bs-theme',theme),theme);
    await app.locator('.ph-article-expand').first().click();
    await page.evaluate(async()=>{await Promise.allSettled(document.getAnimations().filter(a=>Number.isFinite(a.effect?.getComputedTiming().endTime)).map(a=>a.finished));});
    await app.locator('.ph-article-status-trigger').click();
    await app.locator('.ph-article-status-menu').waitFor({state:'visible'});
    await app.evaluate(()=>new Promise(resolve=>requestAnimationFrame(()=>requestAnimationFrame(resolve))));
    const menu=await app.locator('.ph-article-status-menu').evaluate(el=>{const r=el.getBoundingClientRect();return {left:r.left,right:r.right,top:r.top,bottom:r.bottom,width:innerWidth,height:innerHeight,options:[...el.querySelectorAll('.ph-article-status-option')].map(el=>el.textContent.trim())};});
    assert.ok(menu.left>=0&&menu.right<=menu.width&&menu.top>=0&&menu.bottom<=menu.height,JSON.stringify(menu));
    assert.equal(menu.options.length,4);
    await page.screenshot({path:path.join(out,`final-menu-${width}-${theme}.png`)});
    await page.keyboard.press('Escape');
    assert.equal(await app.locator('.ph-article-status-trigger').getAttribute('aria-expanded'),'false');
    await page.screenshot({path:path.join(out,`final-${width}-${theme}.png`)});
    cases.push({width,theme,menu});
  }
  await fs.writeFile(path.join(out,'final-visual-verification.json'),JSON.stringify({ok:true,cases},null,2));
  console.log(JSON.stringify({ok:true,dropdownCases:cases.length}));
} finally {await browser.close();}
