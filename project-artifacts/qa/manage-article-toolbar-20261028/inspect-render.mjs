import {chromium} from 'playwright-core';
import fs from 'node:fs/promises';
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
  const page=await browser.newPage({ignoreHTTPSErrors:true,viewport:{width:1440,height:1000}}),errors=[];
  page.on('pageerror',e=>errors.push(e.message));
  page.on('console',m=>{if(m.type()==='error'||m.text().includes('[Vue warn]'))errors.push(m.text().slice(0,800));});
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  await page.locator('#ph-app-manage-article').waitFor({state:'attached'});
  const result=await page.evaluate(()=>({title:document.title,cloaked:document.querySelector('.ph-article-board')?.hasAttribute('v-cloak'),rows:document.querySelectorAll('tr[data-article-id]').length,summary:document.querySelector('.ph-article-results')?.innerHTML}));
  await page.screenshot({path:'project-artifacts/qa/manage-article-toolbar-20261028/render-diagnostic.png'});
  await fs.writeFile('project-artifacts/qa/manage-article-toolbar-20261028/render-diagnostic.json',JSON.stringify({result,errors},null,2));
  console.log(JSON.stringify({result,errors}));
} finally {await browser.close();}
