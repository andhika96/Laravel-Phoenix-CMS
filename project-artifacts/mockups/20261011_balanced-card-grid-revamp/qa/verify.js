async page => {
  const base='http://127.0.0.1:8777/';
  const output='D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261011_balanced-card-grid-revamp/qa/';
  const checks=[],errors=[];
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  page.on('response',response=>{if(response.status()>=400)errors.push(`${response.status()} ${response.url()}`);});
  await page.setViewportSize({width:1440,height:1000});
  await page.goto(base+'index.html');await page.evaluate(()=>document.fonts.ready);
  check('Default Balanced grid renders six fixture articles',await page.locator('.article-card').count()===6);
  const initial=await page.evaluate(()=>{const input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button'),category=document.querySelector('[name=category]');return {input:input.getBoundingClientRect().height,button:button.getBoundingClientRect().height,category:category.getBoundingClientRect().height,gap:button.getBoundingClientRect().left-input.getBoundingClientRect().right,width:input.parentElement.getBoundingClientRect().width,font:getComputedStyle(input).fontSize,body:getComputedStyle(document.querySelector('.article-page')).fontSize};});
  check('Search input, button, and category align at 48px',initial.input===48&&initial.button===48&&initial.category===48);
  check('Search field is spacious on desktop',initial.width>=600);
  check('Search gap defaults to 12px',Math.abs(initial.gap-12)<.1);
  check('Controls use CMS font size',initial.font===initial.body);
  check('Category retains existing asynchronous filtering attribute',await page.locator('[name=category][data-article-category-select]').count()===1);
  check('Default pagination has no footer frame',await page.locator('.article-pagination--without-frame').count()===1);
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','20px'));
  check('Search follows changes to the CMS font size',await page.locator('[name=search]').evaluate((element,previous)=>parseFloat(getComputedStyle(element).fontSize)>parseFloat(previous)&&getComputedStyle(element).fontSize===getComputedStyle(document.querySelector('.article-page')).fontSize,initial.font));
  await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','14px'));
  for(const [width,columns] of [[1440,3],[1024,3],[768,2],[390,1],[375,1],[320,1]]){
    await page.setViewportSize({width,height:1000});
    const metrics=await page.evaluate(()=>{const cards=[...document.querySelectorAll('.article-card')],first=cards[0].getBoundingClientRect(),row=cards.filter(card=>Math.abs(card.getBoundingClientRect().top-first.top)<1);return {overflow:document.documentElement.scrollWidth>innerWidth,columns:getComputedStyle(document.querySelector('.article-card-grid')).gridTemplateColumns.split(' ').length,heights:row.map(card=>card.getBoundingClientRect().height),footers:row.map(card=>card.querySelector('.article-balanced-footer').getBoundingClientRect().bottom),image:cards[0].querySelector('.article-card__media').getBoundingClientRect().toJSON(),body:cards[0].querySelector('.article-card__body').getBoundingClientRect().toJSON()};});
    check(`No horizontal overflow at ${width}px`,!metrics.overflow);
    check(`Grid has ${columns} columns at ${width}px`,metrics.columns===columns);
    check(`Cards keep equal height in each row at ${width}px`,Math.max(...metrics.heights)-Math.min(...metrics.heights)<1);
    check(`Card footers align within each row at ${width}px`,Math.max(...metrics.footers)-Math.min(...metrics.footers)<1);
    if(width<=575)check(`Mobile photo stays above card text at ${width}px`,metrics.body.top>=metrics.image.bottom);
    if([1440,768,390].includes(width)){const device=width===1440?'desktop':width===768?'tablet':'mobile';await page.screenshot({path:output+device+'.png'});await page.screenshot({path:output+device+'-full.png',fullPage:true});}
  }
  await page.setViewportSize({width:1440,height:1000});
  await page.locator('[name=search]').fill('resilient');await page.locator('[data-article-filter]').evaluate(form=>form.requestSubmit());
  check('Demo search filters to a matching article',await page.locator('.article-card:visible').count()===1);
  await page.locator('[name=search]').fill('nothing-matches-this');await page.locator('[data-article-filter]').evaluate(form=>form.requestSubmit());
  check('Demo empty state is visible',await page.locator('.preview-empty').isVisible()&&await page.locator('.article-card:visible').count()===0);
  await page.locator('[name=search]').fill('');await page.locator('[name=category]').selectOption({label:'Design'});
  check('Demo category filters the article list',await page.locator('.article-card:visible').count()===1);
  await page.locator('[name=category]').selectOption('');await page.locator('.article-text-link').first().click();
  check('Demo opens article dialog',await page.locator('dialog').isVisible());await page.keyboard.press('Escape');
  check('Dialog supports Escape',!await page.locator('dialog').isVisible());
  for(const style of ['flat','outline','elevated']){
    await page.goto(base+'implementation/'+style+'.html');
    const card=await page.locator('.article-card').first().evaluate(element=>{const s=getComputedStyle(element);return {border:s.borderColor,background:s.backgroundColor,shadow:s.boxShadow};});
    check(`${style} card style is preserved`,style==='flat'?card.border==='transparent'||card.border==='rgba(0, 0, 0, 0)':style==='elevated'?card.shadow!=='none':card.shadow==='none'&&card.background==='rgb(255, 255, 255)');
  }
  for(const model of ['boxed','underline','soft']){
    await page.goto(base+'implementation/'+model+'.html');
    check(`${model} uses the shared pagination model`,await page.locator('.article-pagination--model-'+model).count()===1);
    check(`${model} page controls stay 44px`,await page.locator('.page-item.active .page-link').evaluate(element=>element.getBoundingClientRect().height)===44);
    check(`${model} respects default frame OFF`,await page.locator('.article-pagination--without-frame').count()===1);
  }
  await page.goto(base+'implementation/custom.html');
  const custom=await page.evaluate(()=>{const card=document.querySelector('.article-card'),body=card.querySelector('.article-card__body'),media=card.querySelector('.article-card__media'),input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button'),pager=document.querySelector('.article-pagination');return {padding:getComputedStyle(body).paddingTop,radius:getComputedStyle(card).borderTopLeftRadius,align:getComputedStyle(body).textAlign,lines:getComputedStyle(card.querySelector('.article-excerpt-clamp')).webkitLineClamp,mediaHeight:media.getBoundingClientRect().height,mediaRadius:getComputedStyle(media).borderTopLeftRadius,fit:getComputedStyle(media).backgroundSize,searchGap:button.getBoundingClientRect().left-input.getBoundingClientRect().right,searchRadius:getComputedStyle(input).borderTopLeftRadius,searchColor:getComputedStyle(input).backgroundColor,searchLeft:input.getBoundingClientRect().left<document.querySelector('[name=category]').getBoundingClientRect().left,frame:getComputedStyle(pager).borderTopWidth,frameRadius:getComputedStyle(pager).borderTopLeftRadius};});
  check('Card padding and radius follow settings',custom.padding==='24px'&&custom.radius==='20px');
  check('Centered alignment and excerpt lines follow settings',custom.align==='center'&&custom.lines==='4');
  check('Thumbnail height, fit, and frame follow settings',custom.mediaHeight===180&&custom.fit==='contain'&&custom.mediaRadius==='18px');
  check('Search gap, radius, color, and position follow settings',Math.abs(custom.searchGap-18)<.1&&custom.searchRadius==='16px'&&custom.searchColor==='rgb(238, 247, 242)'&&custom.searchLeft);
  check('Explicit pagination frame remains active',custom.frame==='3px'&&custom.frameRadius==='18px');
  check('Pagination total OFF is respected',await page.locator('.article-pagination__summary').count()===0);
  check('Pagination number gap follows settings',await page.locator('.ph-pagination').evaluate(element=>getComputedStyle(element).gap==='12px'));
  check('Article heading tag follows settings',await page.locator('.article-title-clamp').first().evaluate(element=>element.tagName==='H3'));
  check('Centered footer alignment follows settings',await page.locator('.article-balanced-footer').first().evaluate(element=>getComputedStyle(element).justifyContent==='center'));
  for(const [width,gap,padding] of [[1440,40,26],[768,28,22],[390,24,18]]){
    await page.setViewportSize({width,height:1000});
    const grid=await page.locator('.article-card-grid').evaluate(element=>({gap:getComputedStyle(element).gap,columns:getComputedStyle(element).gridTemplateColumns.split(' ').length}));
    check(`Configured story gap stays ${gap}px at ${width}px`,parseFloat(grid.gap)===gap);
    check(`Custom two-column grid remains active at ${width}px`,grid.columns===2);
    check(`Custom shell padding remains active at ${width}px`,await page.locator('.article-shell').evaluate(element=>parseFloat(getComputedStyle(element).paddingTop))===padding);
  }
  await page.goto(base+'implementation/details-off.html');
  check('Story detail toggles remain functional',await page.locator('.article-chip,.article-excerpt-clamp,.article-balanced-footer time,.article-text-link').count()===0);
  await page.goto(base+'implementation/asset.html');
  check('Asset mode renders actual images and configured fit',await page.locator('.article-card__media img').first().evaluate(element=>element.complete&&getComputedStyle(element).objectFit==='contain'));
  await page.goto(base+'implementation/empty.html');
  check('Server empty state remains functional',await page.locator('.article-empty').count()===1&&await page.locator('.article-card,.article-pagination').count()===0);
  await page.goto(base+'implementation/filtered.html');
  check('Server filtered result summary remains functional',await page.locator('.article-story-results [data-article-category-link]').count()===1);
  await page.goto(base+'preview.html');await page.locator('[data-width="390"]').click();
  check('Viewer mobile iframe fits its frame',await page.locator('.frame').evaluate(element=>element.getBoundingClientRect().width<=390));
  await page.screenshot({path:output+'viewer.png'});
  check('All local resources load',errors.length===0);
  await page.evaluate(results=>{window.__balancedResults=results;},{passed:checks.length,checks,errors});
}
