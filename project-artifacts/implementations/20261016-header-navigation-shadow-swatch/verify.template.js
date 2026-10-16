async page => {
    const baseline=BASELINE_DATA,checks=[];
    const check=(ok,message)=>{if(!ok)throw new Error(message);checks.push(message);};
    page.setDefaultTimeout(8000);page.removeAllListeners('dialog');page.on('dialog',d=>d.type()==='beforeunload'?d.accept():d.dismiss());
    await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation');await page.locator('.header-settings-tabbed').waitFor();
    await page.waitForFunction(()=>document.querySelectorAll('.header-menu a').length>0);
    for(const field of baseline.fields){check(await page.locator('#'+field.id).count()===1,'Field retained '+field.id);if(field.options)check(JSON.stringify(await page.locator('#'+field.id+' option').evaluateAll(es=>es.map(e=>e.value)))===JSON.stringify(field.options),'Options retained '+field.id);}
    for(const group of baseline.groups)check(JSON.stringify(await page.locator('[data-control='+group.name+'] [data-value]').evaluateAll(es=>es.map(e=>e.dataset.value)))===JSON.stringify(group.values),'Segmented options retained '+group.name);
    for(const id of baseline.linked)check(await page.locator('#'+id).count()===1,'Linked control retained '+id);
    await page.locator('#linkColor').locator('..').locator('..').screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-shadow-swatch/qa/solid-swatch.png'});
    const swatches=await page.locator('.coloris-control .clr-field button').evaluateAll(es=>es.map(e=>{const s=getComputedStyle(e),p=getComputedStyle(e,'::after');return {border:s.borderStyle,color:s.borderColor,clip:s.backgroundClip,overflow:s.overflow,pseudoShadow:p.boxShadow,pseudoRadius:p.borderRadius};}));
    check(swatches.every(s=>s.border==='solid'&&!s.color.startsWith('rgba')&&s.clip.split(', ').every(c=>c==='padding-box')&&s.overflow==='hidden'&&s.pseudoShadow==='none'&&s.pseudoRadius==='0px'),'Every swatch has one opaque solid edge with clipped checkerboard');
    await page.locator('summary').filter({hasText:'Link shadow'}).click();
    await page.locator('#linkShadowEnabled').check();
    const existingColor=await page.locator('#linkShadowColor').inputValue();
    const checker=await page.locator('#linkShadowColor').locator('..').locator('button').evaluate(e=>getComputedStyle(e).backgroundImage);
    check(checker.includes('repeating-linear-gradient'),'Transparent swatch retains alpha checkerboard');
    const spacing=await page.locator('.shadow-control-meta').evaluate(e=>{const field=e.querySelector('.clr-field'),toggle=e.querySelector('.shadow-inset-check');return {gap:parseFloat(getComputedStyle(e).rowGap),clearance:toggle.getBoundingClientRect().top-field.getBoundingClientRect().bottom};});
    check(spacing.gap===20&&spacing.clearance>=19,'Shadow color and Inset have 20px separation');
    await page.locator('.shadow-control-meta').screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-shadow-swatch/qa/shadow-spacing.png'});
    for(const [id,value] of [['linkShadowX','0'],['linkShadowY','0'],['linkShadowBlur','8'],['linkShadowSpread','4'],['linkShadowColor','#00000080']]){await page.locator('#'+id).fill(value);await page.locator('#'+id).dispatchEvent('change');await page.locator('#'+id).blur();}
    const read=async()=>({config:JSON.parse(await page.locator('#jsonOutput').textContent()).effects.link_shadow,shadow:await page.locator('.header-menu a.is-active').evaluate(e=>getComputedStyle(e).boxShadow)});
    await page.locator('#linkShadowInset').uncheck();const outside=await read();
    check(!outside.config.inset&&!outside.shadow.includes('inset')&&outside.shadow!=='none','Inset off renders an outer shadow');
    await page.locator('#siteHeader').screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-shadow-swatch/qa/outer-shadow.png'});
    await page.locator('#linkShadowInset').check();const inside=await read();
    check(inside.config.inset&&inside.config.value.startsWith('inset ')&&inside.shadow.includes('inset'),'Inset on reaches serialized config and computed preview shadow');
    for(const key of ['x','y','blur','spread','color'])check(outside.config[key]===inside.config[key],'Inset reuses '+key);
    check(await page.locator('#linkShadowInset').isEnabled(),'Inset remains an enabled boolean option');
    await page.locator('#siteHeader').screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261016-header-navigation-shadow-swatch/qa/inner-shadow.png'});
    let payload;
    await page.route('**/awesome_admin/header-navigation',r=>{if(r.request().method()!=='POST')return r.continue();payload=r.request().postDataJSON();return r.fulfill({status:200,contentType:'application/json',body:JSON.stringify({success:true})});});
    await page.locator('#saveHeaderNavigation').click();await page.waitForFunction(()=>document.querySelector('#headerNavigationSaveStatus').classList.contains('is-success'));
    check(payload.config_json.effects.link_shadow.inset===true,'Inset is included in save payload (intercepted)');
    await page.unrouteAll({behavior:'wait'});await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation');await page.locator('.header-settings-tabbed').waitFor();
    return {checks,retainedFields:baseline.fields.length,swatches:swatches.length,spacing,outside,inside,originalShadowColor:existingColor,productionSettingsSaved:false};
}

