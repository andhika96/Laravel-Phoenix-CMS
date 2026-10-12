async page => {
  const base='http://127.0.0.1:8776/implementation/';
  const output='D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261011_mosaic-templates-revamp/implementation/';
  const checks=[],errors=[],models={};
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  page.on('response',response=>{if(response.status()>=400)errors.push(`${response.status()} ${response.url()}`);});
  for(const template of ['magazine','classic']){
    await page.setViewportSize({width:1440,height:1000});
    await page.goto(base+template+'-default.html');
    await page.evaluate(()=>document.fonts.ready);
    const toolbar=await page.evaluate(()=>{const input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button'),i=input.getBoundingClientRect(),b=button.getBoundingClientRect();return {width:input.parentElement.getBoundingClientRect().width,gap:b.left-i.right,input:i.height,button:b.height,font:getComputedStyle(input).fontSize,body:getComputedStyle(document.querySelector('.article-page')).fontSize};});
    check(`${template} toolbar has a spacious search field`,toolbar.width>=600);
    check(`${template} search controls align at 48px`,toolbar.input===48&&toolbar.button===48);
    check(`${template} search gap defaults to 12px`,Math.abs(toolbar.gap-12)<.1);
    check(`${template} search font inherits the CMS body`,toolbar.font===toolbar.body);
    check(`${template} source keeps category auto filtering`,await page.locator('[name=category][data-article-category-select]').count()===1);
    check(`${template} default pagination frame is OFF`,await page.locator('.article-pagination--without-frame').count()===1);
    if(template==='classic')check('Classic Read More remains opt-in',await page.locator('.article-text-link').count()===0);
    await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','20px'));
    check(`${template} search follows a changed CMS font size`,await page.locator('[name=search]').evaluate((element,previous)=>getComputedStyle(element).fontSize===getComputedStyle(document.querySelector('.article-page')).fontSize&&parseFloat(getComputedStyle(element).fontSize)>parseFloat(previous),toolbar.font));
    await page.evaluate(()=>document.documentElement.style.setProperty('--ph-font-size','14px'));
    for(const width of [1440,1024,768,390,375,320]){
      await page.setViewportSize({width,height:1000});
      const layout=await page.evaluate(()=>{const grid=document.querySelector('.article-mosaic-grid--regular,.article-classic-grid');return {overflow:document.documentElement.scrollWidth>innerWidth,columns:getComputedStyle(grid).gridTemplateColumns.split(' ').length};});
      check(`${template} source fits ${width}px`,!layout.overflow);
      check(`${template} source grid adapts at ${width}px`,layout.columns===(width<=575?1:width<=991?2:3));
      if(width===390&&template==='classic')check('Classic mobile side photo has its own configurable height',await page.locator('.article-classic-sidebar__media').first().evaluate(element=>element.getBoundingClientRect().width===element.parentElement.getBoundingClientRect().width&&element.getBoundingClientRect().height===224));
      if(width===1440||width===390){const device=width===1440?'desktop':'mobile';await page.screenshot({path:output+template+'-'+device+'-source.png'});await page.screenshot({path:output+template+'-'+device+'-source-full.png',fullPage:true});}
    }
    models[template]={};
    await page.setViewportSize({width:1440,height:1000});
    for(const type of ['boxed','underline','soft']){
      await page.goto(base+template+'-'+type+'.html');
      check(`${template} renders the ${type} model`,await page.locator('.article-pagination--model-'+type).count()===1);
      const style=await page.locator('.page-item.active .page-link').evaluate(element=>{const s=getComputedStyle(element);return {height:element.getBoundingClientRect().height,border:s.borderBottomWidth,radius:s.borderRadius,background:s.backgroundColor,color:s.color};});
      check(`${template} ${type} pagination has 44px controls`,style.height===44);
      check(`${template} ${type} remains frameless by default`,await page.locator('.article-pagination--without-frame').count()===1);
      models[template][type]=style;
    }
    await page.goto(base+template+'-settings.html');
    check(`${template} regular thumbnail uses its configured 180px height`,await page.locator('.article-mosaic-grid--regular .article-media-frame,.article-classic-grid .article-media-frame').first().evaluate(element=>element.getBoundingClientRect().height===180));
    check(`${template} custom thumbnail frame and fit remain active`,await page.locator('.article-mosaic-grid--regular .article-media-frame,.article-classic-grid .article-media-frame').first().evaluate(element=>{const s=getComputedStyle(element);return s.borderTopLeftRadius==='18px'&&s.borderTopWidth==='2px'&&s.backgroundSize==='contain';}));
    check(`${template} explicit pagination frame remains active`,await page.locator('.article-pagination').evaluate(element=>{const s=getComputedStyle(element);return s.borderTopWidth==='3px'&&s.borderTopLeftRadius==='18px';}));
    check(`${template} pagination number gap stays configurable`,await page.locator('.ph-pagination').evaluate(element=>getComputedStyle(element).gap==='12px'));
    check(`${template} pagination total can be hidden`,await page.locator('.article-pagination__summary').count()===0);
    check(`${template} search gap/radius/color follow settings`,await page.locator('[name=search]').evaluate(element=>{const button=element.parentElement.querySelector('button'),s=getComputedStyle(element);return Math.abs(button.getBoundingClientRect().left-element.getBoundingClientRect().right-18)<.1&&s.borderTopLeftRadius==='16px'&&s.backgroundColor==='rgb(238, 247, 242)';}));
    check(`${template} search position left is respected`,await page.locator('[name=search]').evaluate(element=>element.getBoundingClientRect().left<document.querySelector('[name=category]').getBoundingClientRect().left));
    if(template==='magazine')check('Magazine supporting count/one-column layout follow settings',await page.locator('.article-mosaic-grid--supporting article').count()===1&&await page.locator('.article-mosaic-grid--supporting').evaluate(element=>getComputedStyle(element).gridTemplateColumns.split(' ').length===1));
    if(template==='classic'){
      check('Classic left sidebar position is respected',await page.locator('.article-classic-sidebar').evaluate(element=>element.getBoundingClientRect().left<document.querySelector('.article-classic-lead').getBoundingClientRect().left));
      check('Classic divider OFF hides column and item borders',await page.locator('.article-classic-sidebar').evaluate(element=>getComputedStyle(element).borderInlineStartWidth==='0px')&&await page.locator('.article-classic-sidebar__item').first().evaluate(element=>getComputedStyle(element).borderBottomWidth==='0px'));
      check('Classic image-first lead layout is respected',await page.locator('.article-classic-lead__media').evaluate(element=>element.getBoundingClientRect().top<document.querySelector('.article-classic-lead__body').getBoundingClientRect().top));
      check('Classic visibility toggles/numbering/Read More remain active',await page.locator('.article-chip,.article-classic-meta,.article-excerpt-clamp').count()===0&&await page.locator('.article-classic-number').count()===3&&await page.locator('.article-text-link').count()===12);
    }
    for(const [width,gap,lead,side,padding] of [[1440,40,300,180,26],[768,28,240,140,22],[390,24,200,200,18]]){
      await page.setViewportSize({width,height:1000});
      check(`${template} custom grid columns remain active at ${width}px`,await page.locator('.article-mosaic-grid--regular,.article-classic-grid').evaluate(element=>getComputedStyle(element).gridTemplateColumns.split(' ').length===2));
      check(`${template} custom shell padding stays active at ${width}px`,await page.locator('.article-shell').evaluate(element=>parseFloat(getComputedStyle(element).paddingTop))===padding);
      if(template==='classic'){
        check(`Classic story gap remains ${gap}px at ${width}px`,await page.locator('.article-classic-layout').evaluate(element=>parseFloat(getComputedStyle(element).gap))===gap);
        check(`Classic region heights follow settings at ${width}px`,await page.locator('.article-classic-lead__media').evaluate(element=>element.getBoundingClientRect().height)===lead&&await page.locator('.article-classic-sidebar__media').first().evaluate(element=>element.getBoundingClientRect().height)===side);
      }
    }
    await page.setViewportSize({width:1440,height:1000});
    await page.goto(base+template+'-asset.html');
    check(`${template} asset thumbnails render actual images and preserve fit`,await page.locator('.article-mosaic-grid--regular .article-media-frame img,.article-classic-grid .article-media-frame img').first().evaluate(element=>getComputedStyle(element).objectFit==='contain'&&element.complete));
    await page.goto(base+template+'-filtered.html');
    check(`${template} server filtering removes featured layout`,await page.locator('.article-mosaic-feature,.article-classic-layout').count()===0);
  }
  for(const type of ['boxed','underline','soft'])check(`${type} pagination has identical styling across both Mosaic templates`,JSON.stringify(models.magazine[type])===JSON.stringify(models.classic[type]));
  check('All source fixtures load their local resources',errors.length===0);
  await page.goto(base+'magazine-default.html');
  await page.evaluate(results=>{window.__mosaicSourceResults=results;},{passed:checks.length,checks,errors,models});
}
