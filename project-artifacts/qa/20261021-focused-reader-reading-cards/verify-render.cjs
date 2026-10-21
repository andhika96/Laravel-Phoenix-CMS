const fs=require('node:fs');
const path=require('node:path');
const assert=require('node:assert/strict');
module.exports=async function verify(ctx,editorPage){
 const result=[]; const differences=[];const page=await ctx.newPage();
 const selectors=['h2','h3','h4','p','strong','i','u','s','code','a','blockquote','.text-tiny','.text-small','.text-big','.text-huge','.marker-yellow','.marker-green','.marker-pink','.marker-blue','.pen-red','.pen-green','ul:not(.todo-list)','ol','.todo-list','.todo-list__label','pre','hr','.image','.image-style-align-left','.image-style-align-right','.image-style-align-center','.image-style-side','.image-style-block-align-left','.image-style-block-align-right','.image-inline','.image > figcaption','.table','.table table','.table th','.table td'];
 selectors.push('span[style*="font-family"]','span[style*="color"]','p[style*="text-align:center"]','p[style*="text-align:right"]','p[style*="text-align:justify"]');
 const props=['fontFamily','fontSize','fontWeight','fontStyle','color','backgroundColor','lineHeight','textAlign','textDecorationLine','listStyleType','borderLeftStyle','borderLeftWidth','borderLeftColor','borderCollapse','verticalAlign','float','clear','objectFit','marginTop','marginBottom','marginLeft','marginRight','paddingTop','paddingBottom','paddingLeft','paddingRight'];
 async function snapshot(p){return p.locator('.article-rich-content').evaluate((root,{selectors,props})=>Object.fromEntries(selectors.map(selector=>{const e=root.querySelector(selector);return [selector,e?Object.fromEntries(props.map(key=>[key,getComputedStyle(e)[key]])):null]})),{selectors,props})}
 try {
  for(const width of [320,390,834,1440]){
   await editorPage.setViewportSize({width,height:900});
   const editor=await snapshot(editorPage);
   for(const key of ['focused-reader','editorial-feature','knowledge-toc']){
    await page.setViewportSize({width,height:900});
    await page.setContent(fs.readFileSync(path.join(__dirname,'saved-'+key+'.html'),'utf8'));
    await page.waitForLoadState('networkidle');
    await page.evaluate(()=>document.fonts.ready);
    const actual=await snapshot(page);
    const diff=[];
    for(const selector of selectors){
     // Inline images have an editor-only selection wrapper, absent from saved HTML.
     if(selector==='.image-inline')continue;
     if(!editor[selector]||!actual[selector]){diff.push({selector,missing:true});continue}
     for(const prop of props){
      // Auto margins depend on each template's column width; compare authored CSS separately.
      if((prop==='marginLeft'||prop==='marginRight')&&(selector.startsWith('.image')||selector==='.table'))continue;
      if(editor[selector][prop]!==actual[selector][prop])diff.push({selector,prop,editor:editor[selector][prop],actual:actual[selector][prop]});
     }
    }
    const metrics=await page.locator('.article-rich-content').evaluate(e=>({width:e.getBoundingClientRect().width,overflow:document.documentElement.scrollWidth>innerWidth,images:[...e.querySelectorAll('img')].map(i=>({loaded:i.complete&&i.naturalWidth>0,alt:i.alt})),table:getComputedStyle(e.querySelector('table')).display,checks:[...e.querySelectorAll('input[type=checkbox]')].map(c=>({checked:c.checked,disabled:c.disabled})),rowspan:e.querySelector('[rowspan]')?.getAttribute('rowspan'),colspan:e.querySelector('[colspan]')?.getAttribute('colspan')}));
    assert.equal(metrics.overflow,false,key+' overflow at '+width);
    assert.equal(metrics.images.length,9);assert.ok(metrics.images.every(i=>i.loaded&&i.alt));
    assert.equal(metrics.table,'table');assert.deepEqual(metrics.checks,[{checked:true,disabled:true},{checked:false,disabled:true}]);
    assert.equal(metrics.rowspan,'2');assert.equal(metrics.colspan,'2');
    result.push({key,width,metrics,differences:diff});differences.push(...diff.map(d=>({key,width,...d})));
    if(width===390||width===1440)await page.screenshot({path:path.join(__dirname,`${key}-${width}.png`),fullPage:true});
   }
  }
  fs.writeFileSync(path.join(__dirname,'render-verification.json'),JSON.stringify({cases:result,differences},null,2));
  assert.equal(differences.length,0,'Computed editor/publishing differences: '+JSON.stringify(differences.slice(0,12)));
  console.log(JSON.stringify({cases:result.length,differences:differences.length,first:differences.slice(0,18)}));
  return {cases:result.length,differences:differences.length,first:differences.slice(0,18)};
 }finally{await page.close()}
};
