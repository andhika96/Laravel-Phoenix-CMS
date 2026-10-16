async page => {
    const checks=[],sizes=[];const check=(ok,msg)=>{if(!ok)throw new Error(msg);checks.push(msg);};
    page.removeAllListeners('dialog');page.on('dialog',d=>d.type()==='beforeunload'?d.accept():d.dismiss());
    await page.setViewportSize({width:1440,height:900});
    await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation');await page.locator('.header-settings-tabbed').waitFor();
    const original=await page.locator('#jsonOutput').textContent();
    check(await page.locator('.preview-note').count()===3,'Three simple editorial notes');
    for(const device of ['desktop','tablet','mobile']){
        await page.locator('[data-preview-device='+device+']').click();
        await page.locator('#deviceStage').evaluate(e=>e.scrollTop=e.scrollHeight);
        await page.locator('.preview-feature img').evaluate(img=>img.decode());
        await page.waitForFunction(()=>{const e=document.querySelector('#deviceStage');return Math.abs(e.scrollTop-(e.scrollHeight-e.clientHeight))<3;});
        const g=await page.locator('#deviceStage').evaluate(e=>{const r=e.getBoundingClientRect(),f=e.querySelector('.preview-journal-footer').getBoundingClientRect(),img=e.querySelector('.preview-feature img');return {scroll:e.scrollHeight-e.clientHeight,overflow:e.scrollWidth-e.clientWidth,footerGap:r.bottom-f.bottom,footerVisible:f.bottom>r.top&&f.bottom<=r.bottom+1,image:img.naturalWidth>0};});
        check(g.scroll>0,'Native scrolling available '+device);check(g.overflow<2,'No content horizontal overflow '+device);check(g.footerVisible&&g.footerGap<70,'Content reaches bottom without blank filler '+device);check(g.image,'Local story image loaded '+device);sizes.push({device,...g});
        await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-preview-content/qa/bottom-'+device+'.png'});
    }
    await page.locator('[data-preview-device=desktop]').click();
    await page.locator('#deviceStage').evaluate(e=>e.scrollTop=250);
    await page.waitForTimeout(350);
    await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-preview-content/qa/journal-desktop.png'});
    await page.locator('[data-inspector-tab=advanced]').click();await page.locator('[data-control=headerBehavior] [data-value=sticky]').click();await page.locator('#animateScroll').check();
    await page.locator('.header-preview-options > summary').click();await page.locator('#headerPreviewCondition').selectOption('50');
    await page.waitForFunction(()=>document.querySelector('#siteHeader').classList.contains('is-scrolled'));
    check(true,'Header scroll state works with new content');await page.locator('#resetPreview').click();await page.waitForFunction(()=>document.querySelector('#deviceStage').scrollTop===0);check(true,'Reset returns preview to top');
    await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation');await page.locator('.header-settings-tabbed').waitFor();
    check(await page.locator('#jsonOutput').textContent()===original,'Saved header configuration unchanged');
    return {checks,sizes,productionSettingsSaved:false};
}
