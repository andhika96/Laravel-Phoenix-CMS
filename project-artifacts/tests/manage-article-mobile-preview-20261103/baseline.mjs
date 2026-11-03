import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const out=path.resolve('project-artifacts/qa/manage-article-mobile-preview-20261103');
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
  const page=await browser.newPage({ignoreHTTPSErrors:true,viewport:{width:414,height:1000},hasTouch:true});
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.locator('input[name="email"]').fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.locator('input[type="password"]').fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.locator('button[type="submit"]').filter({hasText:/Log In|Login|Sign In|Masuk/i}).first().click();
  }
  await page.locator('#ph-app-manage-article .ph-article-header-actions').waitFor();
  const metrics=await page.locator('.ph-article-header-actions').evaluate(el=>({fontFamily:getComputedStyle(el).fontFamily,buttons:[...el.children].map(button=>{
    const box=button.getBoundingClientRect(),text=[...button.childNodes].find(node=>node.nodeType===Node.TEXT_NODE&&node.textContent.trim());
    const range=document.createRange();range.selectNodeContents(text);const r=range.getBoundingClientRect(),style=getComputedStyle(button);
    return {label:button.textContent.trim(),height:box.height,minHeight:style.minHeight,display:style.display,padding:style.padding,alignItems:style.alignItems,textOffsetFromCenter:r.top+r.height/2-(box.top+box.height/2)};
  })}));
  await fs.writeFile(path.join(out,'live-button-baseline.json'),JSON.stringify(metrics,null,2));
  await page.screenshot({path:path.join(out,'live-before-414.png')});
  console.log(JSON.stringify(metrics));
} finally {await browser.close();}
