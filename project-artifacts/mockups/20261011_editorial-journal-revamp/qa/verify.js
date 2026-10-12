async page => {
  const base='http://127.0.0.1:8775/';
  const output='D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261011_editorial-journal-revamp/qa/';
  const checks=[],sizes=[],failures=[];
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  page.on('response',response=>{if(response.status()>=400)failures.push(`${response.status()} ${response.url()}`);});
  await page.setViewportSize({width:1440,height:1000});
  await page.goto(base+'index.html');
  await page.evaluate(()=>document.fonts.ready);
  check('One featured story and five editorial cards are present',await page.locator('.article-editorial-lead').count()===1&&await page.locator('.article-editorial-card').count()===5);
  const metrics=()=>page.evaluate(()=>{
    const lead=document.querySelector('.article-editorial-lead'),image=lead.querySelector('.article-editorial-lead__media').getBoundingClientRect(),body=lead.querySelector('.article-editorial-lead__body').getBoundingClientRect();
    const input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button'),i=input.getBoundingClientRect(),b=button.getBoundingClientRect();
    return {width:innerWidth,overflow:document.documentElement.scrollWidth>innerWidth,columns:getComputedStyle(document.querySelector('.article-editorial-grid')).gridTemplateColumns.split(' ').length,image:image.toJSON(),body:body.toJSON(),inputHeight:i.height,buttonHeight:b.height,gap:b.left-i.right,font:parseFloat(getComputedStyle(input).fontSize),pageFont:parseFloat(getComputedStyle(document.querySelector('.article-page')).fontSize),family:getComputedStyle(input).fontFamily};
  });
  let layout=await metrics();
  check('Search controls align at 48px or larger',layout.inputHeight>=48&&Math.abs(layout.inputHeight-layout.buttonHeight)<1);
  check('Search input and action have a 12px gap',Math.abs(layout.gap-12)<1);
  check('Typography follows CMS body scale and Noto Sans',Math.abs(layout.font-layout.pageFont)<.1&&layout.family.includes('Noto Sans'));
  const font=layout.font;
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','20px'));
  layout=await metrics();
  check('CMS font changes update search text',layout.font>font&&Math.abs(layout.font-layout.pageFont)<.1);
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','14px'));
  for(const [width,columns] of [[1440,3],[1024,3],[768,2],[390,1],[375,1],[320,1]]){
    await page.setViewportSize({width,height:1000});
    layout=await metrics();
    check(`No horizontal overflow at ${width}px`,!layout.overflow);
    check(`Editorial grid has ${columns} columns at ${width}px`,layout.columns===columns);
    check(`Featured story ordering is suitable at ${width}px`,width<=991?layout.body.top>=layout.image.bottom:layout.image.left>=layout.body.right);
    sizes.push(layout);
    if(width===1440){await page.screenshot({path:output+'desktop.png'});await page.screenshot({path:output+'desktop-full.png',fullPage:true});}
    if(width===768)await page.screenshot({path:output+'tablet.png'});
    if(width===390){await page.screenshot({path:output+'mobile.png'});await page.screenshot({path:output+'mobile-full.png',fullPage:true});await page.locator('.article-editorial-card').first().screenshot({path:output+'mobile-card.png'});}
  }
  await page.setViewportSize({width:1440,height:1000});
  await page.getByRole('searchbox',{name:'Search articles',exact:true}).fill('resilient');
  await page.getByRole('button',{name:'Search',exact:true}).click();
  check('Demo search finds the featured story',await page.locator('.article-editorial-lead:visible').count()===1&&await page.locator('.article-editorial-card:visible').count()===0);
  await page.getByRole('searchbox',{name:'Search articles',exact:true}).fill('nothing-matches-xyz');
  await page.getByRole('button',{name:'Search',exact:true}).click();
  check('Demo search has a readable empty state',await page.locator('.preview-empty').isVisible());
  await page.getByRole('searchbox',{name:'Search articles',exact:true}).fill('');
  await page.getByRole('button',{name:'Search',exact:true}).click();
  await page.locator('[name=category]').selectOption({label:'Design'});
  check('Demo category filter finds its card',await page.locator('.article-editorial-card:visible').count()===1);
  await page.locator('[name=category]').selectOption('');
  await page.locator('.article-editorial-lead .article-text-link').click();
  check('Read more opens a native sample dialog',await page.locator('#article-dialog').isVisible());
  await page.keyboard.press('Escape');
  check('Escape closes the sample dialog',!await page.locator('#article-dialog').isVisible());
  await page.emulateMedia({reducedMotion:'reduce'});
  check('Reduced motion removes transitions',await page.locator('.article-text-link').first().evaluate(element=>getComputedStyle(element).transitionDuration==='0s'));
  await page.emulateMedia({reducedMotion:'no-preference'});
  await page.setViewportSize({width:1100,height:900});
  await page.goto(base+'preview.html');
  const frame=page.frameLocator('#prototype');
  await frame.locator('.article-editorial-lead').waitFor();
  await page.getByRole('button',{name:'Mobile',exact:true}).click();
  await page.waitForFunction(()=>document.getElementById('prototype').style.width==='390px');
  check('Device viewer switches to mobile without overflow',await frame.locator('body').evaluate(()=>document.documentElement.scrollWidth<=innerWidth));
  await page.getByRole('button',{name:'Desktop',exact:true}).click();
  await page.waitForFunction(()=>document.getElementById('prototype').style.width==='1440px');
  await page.screenshot({path:output+'viewer.png'});
  check('All local resources load',failures.length===0);
  await page.evaluate(results=>{window.__editorialPreviewResults=results;},{passed:checks.length,checks,sizes,failures});
}
