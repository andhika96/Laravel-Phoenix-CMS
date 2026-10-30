import fs from 'node:fs/promises';
import path from 'node:path';
import {chromium} from 'playwright-core';

const out=path.resolve('project-artifacts/qa/manage-article-control-spacing-20261029');
const browser=await chromium.launch({channel:'chrome',headless:true});
try {
  const page=await browser.newPage({ignoreHTTPSErrors:true,viewport:{width:1440,height:1050}});
  await page.goto('https://laravel-13-phoenix.aruna/manage_article');
  if(page.url().includes('/auth/login')) {
    await page.getByRole('textbox',{name:'Enter your email'}).fill(process.env.PH_ARTICLE_QA_EMAIL);
    await page.getByRole('textbox',{name:'Enter your password'}).fill(process.env.PH_ARTICLE_QA_PASSWORD);
    await page.getByRole('button',{name:'Log In',exact:true}).click();
  }
  await page.locator('#ph-app-manage-article tr[data-article-id]').first().waitFor();
  const cases=[];
  for(const width of [1440,1024,768,390,320]) {
    await page.setViewportSize({width,height:1050});
    await page.locator('#ph-app-manage-article').evaluate(()=>new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r))));
    const metrics=await page.locator('#ph-app-manage-article').evaluate(app=>{
      const row=app.querySelector('tr[data-article-id]');
      const read=el=>{const r=el.getBoundingClientRect(),s=getComputedStyle(el);return {x:r.x,y:r.y,width:r.width,height:r.height,padding:s.padding,paddingLeft:s.paddingLeft,paddingRight:s.paddingRight,paddingTop:s.paddingTop,paddingBottom:s.paddingBottom,fontSize:s.fontSize,lineHeight:s.lineHeight,display:s.display,align:s.verticalAlign,textAlign:s.textAlign,boxSizing:s.boxSizing,zoom:s.zoom,visible:el.checkVisibility()};};
      const button=row.querySelector('.ph-article-expand'),check=row.querySelector('.ph-article-check-target'),title=row.querySelector('.ph-article-title');
      const b=button.getBoundingClientRect(),c=check.getBoundingClientRect(),t=title.getBoundingClientRect();
      const rules=[];
      for(const sheet of document.styleSheets){try{const walk=rs=>{for(const rule of rs){if(rule.selectorText){try{if(button.matches(rule.selectorText)||button.parentElement.matches(rule.selectorText))rules.push({sheet:sheet.href,selector:rule.selectorText,style:rule.style.cssText});}catch{}}else if(rule.cssRules&&(!rule.media||matchMedia(rule.conditionText).matches))walk(rule.cssRules);}};walk(sheet.cssRules);}catch{}}
      return {width:innerWidth,rootSize:getComputedStyle(document.documentElement).fontSize,bodySize:getComputedStyle(document.body).fontSize,manager:read(app),row:read(row),expandCell:read(button.parentElement),button:read(button),checkCell:read(check.parentElement),check:read(check),title:read(title),headerCheck:read(app.querySelector('thead .ph-article-check-target')),gap:c.left-b.right,leftInset:b.left-row.getBoundingClientRect().left,centerDelta:(b.top+b.height/2)-(t.top+22/2),overflow:document.documentElement.scrollWidth>innerWidth+1,rules};
    });
    cases.push(metrics);
    if(width===1440||width===390) {
      await page.locator('tr[data-article-id] .ph-article-expand').first().click();
      await page.screenshot({path:path.join(out,`before-${width}.png`)});
      await page.locator('tr[data-article-id] .ph-article-expand').first().click();
    }
  }
  await fs.writeFile(path.join(out,'before.json'),JSON.stringify(cases,null,2));
  console.log(JSON.stringify(cases.map(({rules,...m})=>m)));
} finally {await browser.close();}
