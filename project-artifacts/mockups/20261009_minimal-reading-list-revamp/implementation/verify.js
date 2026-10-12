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
    if(width<=575)check(`Mobile thumbnail fills the row and text follows below at ${width}px`,Math.abs(size.body.left-size.image.left)<1&&size.body.top>=size.image.bottom&&size.image.width>width-48);
    else check(`Desktop and tablet retain side-by-side rows at ${width}px`,size.body.left>size.image.right&&size.body.top<size.image.bottom);
  }
  await page.setViewportSize({width:1440,height:900});
  await page.setViewportSize({width:390,height:900});
  const thumbnail=await page.locator('.article-reading-list__media').first().evaluate(element=>{element.style.setProperty('--article-thumbnail-height','280px');return element.getBoundingClientRect().height;});
  check('Mobile background thumbnail honors its configured height',thumbnail===280);
  await page.setViewportSize({width:1440,height:900});
  for(const model of ['boxed','underline','soft']){
    await page.goto(base+model+'.html');
    const frame=await page.locator('.article-pagination').evaluate(element=>{const s=getComputedStyle(element);return {border:parseFloat(s.borderTopWidth),radius:parseFloat(s.borderTopLeftRadius)};});
    check(`${model} footer stays open without a rounded container`,frame.border===0&&frame.radius===0);
    const style=await page.locator('.ph-pagination .active .page-link').evaluate(element=>{const s=getComputedStyle(element);return {radius:parseFloat(s.borderTopLeftRadius),background:s.backgroundColor,bottom:parseFloat(s.borderBottomWidth)};});
    check(`${model} keeps the selected pagination appearance`,model==='underline'?style.radius===0&&style.bottom===2:style.radius>0);
  }
  await page.goto(base+'settings.html');
  check('Header text follows Content settings',await page.locator('.article-template-header h1').innerText()==='Reading room');
  check('Description toggle removes its output',await page.locator('.article-template-header__copy > p:not(.article-eyebrow)').count()===0);
  check('Sidebar toggle removes its panels',await page.locator('.article-reading-list__sidebar').count()===0);
  check('Article title tag follows its setting',await page.locator('.article-reading-list__body > h2.article-title-clamp').count()===6);
  const settings=await page.evaluate(()=>{
    const input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button'),frame=document.querySelector('.article-pagination'),media=document.querySelector('.article-reading-list__media');
    return {gap:button.getBoundingClientRect().left-input.getBoundingClientRect().right,radius:parseFloat(getComputedStyle(input).borderTopLeftRadius),postGap:parseFloat(getComputedStyle(document.querySelector('.article-reading-list')).rowGap),frameBorder:parseFloat(getComputedStyle(frame).borderTopWidth),frameRadius:parseFloat(getComputedStyle(frame).borderTopLeftRadius),mediaHeight:media.getBoundingClientRect().height,mediaRadius:parseFloat(getComputedStyle(media).borderTopLeftRadius)};
  });
  check('Custom search gap and radius are rendered',settings.gap===18&&settings.radius===16);
  check('Post list gap follows its setting',settings.postGap===24);
  check('Enabled footer frame uses configured border and radius',settings.frameBorder===3&&settings.frameRadius===18);
  check('Footer total toggle removes summary',await page.locator('.article-pagination__summary').count()===0);
  check('Configured thumbnail height and frame radius are rendered',settings.mediaHeight===280&&settings.mediaRadius===18);
  await page.setViewportSize({width:390,height:900});
  const mobileMedia=await page.locator('.article-reading-list__media').first().evaluate(element=>({height:element.getBoundingClientRect().height,radius:parseFloat(getComputedStyle(element).borderTopLeftRadius)}));
  check('Mobile preserves custom thumbnail height and frame radius',mobileMedia.height===280&&mobileMedia.radius===18);
  check('Mobile post gap follows its setting',await page.locator('.article-reading-list').evaluate(element=>parseFloat(getComputedStyle(element).rowGap))===24);
  await page.goto(base+'boxed.html');
  await page.evaluate(results=>{window.__readingRevampResults=results;},{passed:checks.length,checks});
}
