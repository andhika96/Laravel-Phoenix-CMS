async page => {
  const base='http://127.0.0.1:8774/implementation/';
  const checks=[];
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  await page.setViewportSize({width:1440,height:900});
  await page.goto(base+'boxed.html');
  await page.evaluate(()=>document.fonts.ready);
  const measure=()=>page.evaluate(()=>{
    const input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button'),image=document.querySelector('.article-reading-list__media'),body=document.querySelector('.article-reading-list__body');
    const i=input.getBoundingClientRect(),b=button.getBoundingClientRect();
    return {gap:b.left-i.right,inputHeight:i.height,buttonHeight:b.height,buttonFont:parseFloat(getComputedStyle(button).fontSize),pageFont:parseFloat(getComputedStyle(document.querySelector('.article-page')).fontSize),family:getComputedStyle(input).fontFamily,overflow:document.documentElement.scrollWidth>innerWidth,image:image.getBoundingClientRect().toJSON(),body:body.getBoundingClientRect().toJSON()};
  });
  let size=await measure();
  check('Search input and button have a visible gap',size.gap>=8);
  check('Search controls align and are at least 48px',size.inputHeight>=48&&Math.abs(size.inputHeight-size.buttonHeight)<1);
  check('Search font inherits frontend CMS body scale',Math.abs(size.buttonFont-size.pageFont)<.1&&size.family.includes('Noto Sans'));
  check('Result heading is inside the AJAX replacement slot',await page.locator('[data-article-vue-list-content] .article-reading-list__heading').count()===1);
  const tokens=await page.evaluate(()=>{const probe=document.createElement('span');probe.style.fontSize='var(--ph-fmv2-rfs-h1)';document.body.append(probe);const expected=getComputedStyle(probe).fontSize;probe.remove();return {expected,actual:getComputedStyle(document.querySelector('.article-template-header h1')).fontSize};});
  check('Main heading follows CMS responsive heading token',tokens.actual===tokens.expected);
  const old=size.buttonFont;
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','20px'));
  size=await measure();
  check('Changing CMS font size changes control text without fixed px overrides',size.buttonFont>old&&Math.abs(size.buttonFont-size.pageFont)<.1);
  check('Larger CMS text keeps aligned search controls',Math.abs(size.inputHeight-size.buttonHeight)<1);
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','14px'));
  for(const width of [1440,1024,768,390,375,320]){
    await page.setViewportSize({width,height:900});
    size=await measure();
    check(`No horizontal overflow at ${width}px`,!size.overflow);
    if(width<=575)check(`Compact thumbnail and text share a row at ${width}px`,size.body.left>size.image.right&&size.body.top<size.image.bottom);
  }
  await page.setViewportSize({width:1440,height:900});
  for(const model of ['boxed','underline','soft']){
    await page.goto(base+model+'.html');
    const frame=await page.locator('.article-pagination').evaluate(element=>{const s=getComputedStyle(element);return {border:parseFloat(s.borderTopWidth),radius:parseFloat(s.borderTopLeftRadius)};});
    check(`${model} footer stays open without a rounded container`,frame.border===0&&frame.radius===0);
    const style=await page.locator('.ph-pagination .active .page-link').evaluate(element=>{const s=getComputedStyle(element);return {radius:parseFloat(s.borderTopLeftRadius),background:s.backgroundColor,bottom:parseFloat(s.borderBottomWidth)};});
    check(`${model} keeps the selected pagination appearance`,model==='underline'?style.radius===0&&style.bottom===2:style.radius>0);
  }
  await page.goto(base+'boxed.html');
  await page.evaluate(results=>{window.__readingRevampResults=results;},{passed:checks.length,checks});
}
