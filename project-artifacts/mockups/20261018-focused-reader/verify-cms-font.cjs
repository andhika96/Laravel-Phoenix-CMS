const {chromium}=require('playwright-core');
const fs=require('node:fs');
const path=require('node:path');
const assert=require('node:assert/strict');
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try {
  const page=await browser.newPage(); const results=[];
  const root=path.resolve(__dirname,'../../..');
  for(const width of [320,390,768,834,1024,1440]) for(const base of [14,18]) {
   await page.setViewportSize({width,height:900});
   await page.setContent(`<style>:root{--ph-font-size:${base}px}</style><main class="ph-content" id="reference"><h1>CMS</h1><p>CMS body</p></main><article class="article-detail article-detail--focused-reader" style="--reader-title-size:80px;--reader-body-size:24px"><h1>Article</h1><p class="article-detail__dek">Summary</p><div class="article-rich-content">Body</div></article>`);
   await page.addStyleTag({path:path.join(root,'public/assets/css/theme-responsive-typography.css')});
   await page.addStyleTag({path:path.join(root,'public/assets/css/article/article-frontend-2026.css')});
   const metrics=await page.evaluate(()=>{
    const size=s=>getComputedStyle(document.querySelector(s)).fontSize;
    const probe=document.createElement('div');probe.style.fontSize=innerWidth<=768?'var(--ph-mobile-content-font-size)':'var(--ph-adaptive-font-size)';document.body.append(probe);
    const expected=getComputedStyle(probe).fontSize;probe.remove();
    return {cms:size('#reference h1'),article:size('.article-detail h1'),body:size('.article-rich-content'),summary:size('.article-detail__dek'),expected};
   });
   assert.equal(metrics.cms,metrics.article);
   assert.equal(metrics.body,metrics.expected);
   assert.equal(metrics.summary,metrics.expected);
   results.push({width,base,...metrics});
  }
  fs.writeFileSync(path.join(__dirname,'cms-font-regression.json'),JSON.stringify(results,null,2));
  console.log(`Passed ${results.length} CMS typography cases, including Site Config changes and legacy pixel overrides.`);
 }finally{await browser.close()}
})().catch(e=>{console.error(e);process.exitCode=1});
