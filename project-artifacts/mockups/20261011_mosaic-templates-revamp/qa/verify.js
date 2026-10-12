async page => {
  const base='http://127.0.0.1:8776/';
  const output='D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261011_mosaic-templates-revamp/qa/';
  const checks=[],failures=[];
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  page.on('response',response=>{if(response.status()>=400)failures.push(`${response.status()} ${response.url()}`);});
  for(const template of ['magazine','classic']){
    await page.setViewportSize({width:1440,height:1000});
    await page.goto(base+template+'.html');
    await page.evaluate(()=>document.fonts.ready);
    check(`${template} retains its article regions`,template==='magazine'?await page.locator('[data-mosaic-region=hero]').count()===1&&await page.locator('[data-mosaic-region=supporting] article').count()===2:await page.locator('.article-classic-lead').count()===1&&await page.locator('.article-classic-sidebar__item').count()===3&&await page.locator('.article-classic-highlight').count()===2);
    const initial=await page.evaluate(()=>{const input=document.querySelector('[name=search]'),button=input.parentElement.querySelector('button');return {gap:button.getBoundingClientRect().left-input.getBoundingClientRect().right,input:input.getBoundingClientRect().height,button:button.getBoundingClientRect().height,font:parseFloat(getComputedStyle(input).fontSize),bodyFont:parseFloat(getComputedStyle(document.querySelector('.article-page')).fontSize),family:getComputedStyle(input).fontFamily};});
    check(`${template} search controls align at 48px`,initial.input>=48&&Math.abs(initial.input-initial.button)<1);
    check(`${template} search gap is 12px`,Math.abs(initial.gap-12)<1);
    check(`${template} uses frontend CMS typography`,Math.abs(initial.font-initial.bodyFont)<.1&&initial.family.includes('Noto Sans'));
    for(const width of [1440,1024,768,390,375,320]){
      await page.setViewportSize({width,height:1000});
      const layout=await page.evaluate(()=>{
        const lead=document.querySelector('.article-mosaic-feature,.article-classic-lead'),media=lead.querySelector('.article-mosaic-feature__media,.article-classic-lead__media'),body=lead.querySelector('.article-mosaic-feature__body,.article-classic-lead__body');
        const grid=document.querySelector('.article-mosaic-grid--regular,.article-classic-grid');
        return {overflow:document.documentElement.scrollWidth>innerWidth,columns:getComputedStyle(grid).gridTemplateColumns.split(' ').length,media:media.getBoundingClientRect().toJSON(),body:body.getBoundingClientRect().toJSON()};
      });
      check(`${template} has no horizontal overflow at ${width}px`,!layout.overflow);
      check(`${template} grid columns adapt at ${width}px`,layout.columns===(width<=575?1:width<=991?2:3));
      if(width<=575&&template==='magazine')check(`Magazine mobile separates photo and text at ${width}px`,layout.body.top>=layout.media.bottom);
      if(width<=575&&template==='classic'){
        const side=await page.locator('.article-classic-sidebar__item').first().evaluate(element=>{const image=element.querySelector('.article-classic-sidebar__media').getBoundingClientRect(),text=element.querySelector('div').getBoundingClientRect();return {image:image.toJSON(),text:text.toJSON(),width:element.getBoundingClientRect().width};});
        check(`Classic mobile uses full-width side-story photos at ${width}px`,side.image.width>=side.width-2&&side.text.top>=side.image.bottom);
      }
      if(width===1440){await page.screenshot({path:output+template+'-desktop.png'});await page.screenshot({path:output+template+'-desktop-full.png',fullPage:true});}
      if(width===768)await page.screenshot({path:output+template+'-tablet.png'});
      if(width===390){await page.screenshot({path:output+template+'-mobile.png'});await page.screenshot({path:output+template+'-mobile-full.png',fullPage:true});}
    }
    await page.setViewportSize({width:1440,height:1000});
    await page.getByRole('searchbox',{name:'Search articles',exact:true}).fill('resilient');
    await page.getByRole('button',{name:'Search',exact:true}).click();
    check(`${template} demo search filters stories`,await page.locator('article:visible').count()===1);
    await page.getByRole('searchbox',{name:'Search articles',exact:true}).fill('no-result-xyz');
    await page.getByRole('button',{name:'Search',exact:true}).click();
    check(`${template} has a search empty state`,await page.locator('.preview-empty').isVisible());
    await page.getByRole('searchbox',{name:'Search articles',exact:true}).fill('');
    await page.getByRole('button',{name:'Search',exact:true}).click();
    await page.locator('[name=category]').selectOption({label:'Design'});
    check(`${template} category filter finds sample stories`,await page.locator('article:visible').count()>0);
    await page.locator('[name=category]').selectOption('');
    await page.locator('article .article-title-clamp a').first().click();
    check(`${template} opens a sample story dialog`,await page.locator('#article-dialog').isVisible());
    await page.keyboard.press('Escape');
    check(`${template} closes dialog with Escape`,!await page.locator('#article-dialog').isVisible());
  }
  await page.setViewportSize({width:1100,height:900});
  await page.goto(base+'preview.html');
  await page.frameLocator('#prototype').locator('.article-mosaic-feature').waitFor();
  await page.getByRole('button',{name:'Mobile',exact:true}).click();
  await page.waitForFunction(()=>document.getElementById('prototype').style.width==='390px');
  check('Viewer mobile fits its iframe',await page.frameLocator('#prototype').locator('body').evaluate(()=>document.documentElement.scrollWidth<=innerWidth));
  await page.getByRole('combobox',{name:'Template',exact:true}).selectOption('classic');
  await page.frameLocator('#prototype').locator('.article-classic-lead').waitFor();
  check('Viewer switches between both templates',await page.frameLocator('#prototype').locator('.article-classic-lead').count()===1);
  await page.getByRole('button',{name:'Desktop',exact:true}).click();
  await page.waitForFunction(()=>document.getElementById('prototype').style.width==='1440px');
  await page.screenshot({path:output+'viewer.png'});
  check('All local resources load',failures.length===0);
  await page.evaluate(results=>{window.__mosaicPreviewResults=results;},{passed:checks.length,checks,failures});
}
