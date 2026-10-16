async page => {
    const baseline = BASELINE_DATA;
    const out = OUTPUT_PATH;
    const report = {checks:[], screenshots:[], errors:[]};
    const check = (condition, message) => { if(!condition) throw new Error(message); report.checks.push(message); };
    const equal = (a,b,message) => check(JSON.stringify(a)===JSON.stringify(b),message);
    page.setDefaultTimeout(8000);
    page.removeAllListeners('pageerror');
    page.on('pageerror',error=>report.errors.push(error.message));
    const reload = async () => { await page.goto('https://laravel-13-phoenix.aruna/awesome_admin/header-navigation'); await page.locator('.header-settings-tabbed').waitFor(); await page.waitForFunction(()=>document.querySelectorAll('.header-menu a').length>1); };
    const config = async () => JSON.parse(await page.locator('#jsonOutput').textContent());
    const tab = async name => page.locator('[data-inspector-tab='+name+']').click();
    const shot = async name => {await page.screenshot({path:out+'/'+name+'.png'}); report.screenshots.push(name);};
    const color = async (id,value) => {const field=page.locator('#'+id);await field.fill(value);await field.dispatchEvent('change');await field.blur();};
    const style = async (selector,property) => page.locator(selector).first().evaluate((e,p)=>getComputedStyle(e)[p],property);
    await page.setViewportSize({width:1440,height:1000});
    await reload();
    for(const f of baseline.fields) {
        check(await page.locator('#'+f.id).count()===1,'Preserved field '+f.id);
        if(f.options) equal(await page.locator('#'+f.id+' option').evaluateAll(es=>es.map(e=>e.value)),f.options,'Preserved options '+f.id);
    }
    for(const g of baseline.groups) equal(await page.locator('[data-control='+g.name+'] [data-value]').evaluateAll(es=>es.map(e=>e.dataset.value)),g.values,'Preserved segmented options '+g.name);
    for(const id of baseline.linked) check(await page.locator('#'+id).count()===1,'Preserved link control '+id);
    const initial=await config();
    equal(initial,baseline.config,'Initial configuration matches original live configuration');
    await tab('layout');await tab('advanced');await tab('style');
    for(const state of ['active','hover','focus','normal']) await page.locator('#headerMenuState').selectOption(state);
    equal(await config(),initial,'Tab and state navigation do not change configuration');
    await page.locator('[data-inspector-tab=style]').focus();await page.keyboard.press('ArrowRight');
    equal(await page.locator('[data-inspector-tab=advanced]').getAttribute('aria-selected'),'true','Arrow-key tab selection');
    await page.keyboard.press('Home');equal(await page.locator('[data-inspector-tab=layout]').getAttribute('aria-selected'),'true','Home-key tab selection');
    await shot('layout');
    await page.locator('summary').filter({hasText:'Sizing'}).click();
    const stepperIds=baseline.fields.filter(f=>f.type==='number'&&!f.id.startsWith('linkShadow')).map(f=>f.id);
    async function testSteppers(ids) {
        for(const id of ids) {
            const field=page.locator('#'+id), before=Number(await field.inputValue());
            const inc=page.locator('[data-stepper-target='+id+'][data-stepper-delta="1"], [data-box-stepper-target='+id+'][data-box-stepper-delta="1"]');
            const dec=page.locator('[data-stepper-target='+id+'][data-stepper-delta="-1"], [data-box-stepper-target='+id+'][data-box-stepper-delta="-1"]');
            await inc.click();check(Number(await field.inputValue())===before+1,'Increment '+id);
            await dec.click();check(Number(await field.inputValue())===before,'Decrement '+id);
            const [minus,input,plus]=await Promise.all([dec.boundingBox(),field.boundingBox(),inc.boundingBox()]);
            check(minus.x+minus.width<=input.x+1&&input.x+input.width<=plus.x+1,'Horizontal stepper '+id);
            const label=page.locator('label[for='+id+']');
            if(await label.count()) {const b=await label.boundingBox();check(b&&b.y+b.height<=input.y+1,'Label clear of field '+id);}
        }
    }
    await testSteppers(stepperIds);
    for(const box of ['headerRadius','headerPadding','navRadius','containerMargin']) {
        const linked=page.locator('#'+box+'Linked');
        if(await linked.getAttribute('aria-pressed')==='true') await linked.click();
        await page.locator('#'+box+'Top').fill('7');await page.locator('#'+box+'Right').fill('9');
        check(await page.locator('#'+box+'Top').inputValue()==='7','Independent dimensions '+box);
        await linked.click();await page.locator('#'+box+'Top').fill('11');
        for(const side of ['Right','Bottom','Left']) equal(await page.locator('#'+box+side).inputValue(),'11','Linked '+box+side);
        const unit=page.locator('#'+box+'Unit');
        for(const value of ['px','%','em','rem','pt']) {await unit.selectOption(value);equal(await unit.inputValue(),value,'Unit '+box+' '+value);}
        await unit.selectOption('px');
        const group=page.locator('[data-responsive-control='+box+']');
        await group.locator('[data-responsive-trigger]').click();await group.locator('[data-responsive-mode=mobile]').click();
        equal(await page.locator('#deviceMode').inputValue(),'mobile','Responsive device '+box);
        await page.locator('#'+box+'Top').fill('13');
        await group.locator('[data-responsive-trigger]').click();await group.locator('[data-responsive-mode=desktop]').click();
        equal(await page.locator('#'+box+'Top').inputValue(),'11','Desktop profile retained '+box);
    }
    for(const name of ['logoPosition','menuPosition','containerMode','linkShape','leafDirection']) {
        if(name==='leafDirection') await page.locator('[data-control=linkShape] [data-value=leaf]').click();
        for(const value of baseline.groups.find(g=>g.name===name).values) {
            await page.locator('[data-control='+name+'] [data-value='+value+']').click();
            equal(await page.locator('[data-control='+name+'] [data-value='+value+']').getAttribute('aria-pressed'),'true','Selectable '+name+' '+value);
        }
    }
    await reload();await tab('layout');await page.locator('summary').filter({hasText:'Sizing'}).click();
    await page.locator('#headerRadiusTop').scrollIntoViewIfNeeded();await shot('sizing-top');
    await page.locator('#containerMarginLeft').scrollIntoViewIfNeeded();await shot('sizing-bottom');
    await tab('style');await color('headerText','#123456');await color('linkColor','#654321');
    equal(await style('.header-logo','color'),'rgb(18, 52, 86)','Header text color preview');
    equal(await style('.header-menu a:not(.is-active)','color'),'rgb(101, 67, 33)','Normal menu text preview');
    for(const state of ['active','hover','focus']) {
        await page.locator('#headerMenuState').selectOption(state);
        const prefix='link'+state[0].toUpperCase()+state.slice(1);
        await color(prefix,'#112233');
        check(await page.locator('#'+prefix+'Border').isDisabled(),'Linked border disabled '+state);
        await page.locator('#'+prefix+'BorderLinked').click();await color(prefix+'Border','#445566');
        equal((await config()).colors['link_'+state+'_border'],'#445566','Independent border '+state);
        await page.locator('#'+prefix+'BorderLinked').click();
        equal(await page.locator('#'+prefix+'Border').inputValue(),'#112233','Linked border sync '+state);
        if(state==='hover') await page.locator('.header-menu a:not(.is-active)').first().hover();
        if(state==='focus') {await page.mouse.move(10,10);await page.locator('.header-menu a:not(.is-active)').first().focus();}
        const selector=state==='active'?'.header-menu a.is-active':'.header-menu a:not(.is-active)';
        await page.waitForFunction(({selector})=>getComputedStyle(document.querySelector(selector)).backgroundColor==='rgb(17, 34, 51)',{selector});
        check(true,'Rendered '+state+' background');
    }
    await page.locator('#headerMenuState').selectOption('normal');
    await page.locator('#headerBg').click({position:{x:14,y:16}});
    check(await page.locator('#clr-picker').isVisible(),'Color picker opens');await shot('color-picker');await page.keyboard.press('Escape');
    await page.locator('summary').filter({hasText:'Link shadow'}).click();await page.locator('#linkShadowEnabled').check();
    await testSteppers(['linkShadowX','linkShadowY','linkShadowBlur','linkShadowSpread']);
    await page.locator('#linkShadowInset').check();await color('linkShadowColor','#11223380');
    check((await config()).effects.link_shadow.inset,'Shadow inset serialized');
    check((await style('.header-menu a.is-active','boxShadow'))!=='none','Shadow reaches preview');await shot('shadow');
    await tab('advanced');
    check(await page.locator('#animateScroll').isDisabled(),'Stay disables scroll animation');
    for(const mode of ['fixed','sticky']) {await page.locator('[data-control=headerBehavior] [data-value='+mode+']').click();check(await page.locator('#animateScroll').isEnabled(),'Scroll animation enabled for '+mode);}
    await page.locator('#animateScroll').check();await page.locator('#transparentStart').check();
    await page.locator('[data-control=transparentColorMode] [data-value=custom]').click();
    for(const id of ['transparentText','transparentHover','transparentFocus','transparentActive']) {check(await page.locator('#'+id).isVisible(),'Custom transparent control '+id);await color(id,'#abcdef');}
    equal(await style('.header-logo','color'),'rgb(171, 205, 239)','Transparent custom text preview');
    await shot('behavior');
    await page.locator('.header-preview-options > summary').click();await page.locator('#headerPreviewCondition').selectOption('50');
    await page.waitForFunction(()=>document.querySelector('#siteHeader').classList.contains('is-scrolled'));
    equal(await style('.header-logo','color'),'rgb(18, 52, 86)','Scrolled restores configured text');
    await page.locator('#resetPreview').click();await page.waitForFunction(()=>document.querySelector('#deviceStage').scrollTop===0);
    check(true,'Scroll preview and reset');await page.keyboard.press('Escape');
    await page.locator('summary').filter({hasText:'Configuration'}).click();check(await page.locator('#jsonOutput').isVisible(),'Configuration remains accessible');
    await page.locator('[data-header-editor-exit]').last().click();check(await page.locator('#headerEditorExitDialog').isVisible(),'Cancel protects unsaved changes');await page.locator('#headerEditorKeepEditing').click();
    const beforeSave=await config();
    await page.route('**/awesome_admin/header-navigation',route=>route.request().method()==='POST'?route.fulfill({status:500,contentType:'application/json',body:JSON.stringify({success:false,message:'Verification: save failed'})}):route.fallback());
    await page.locator('#saveHeaderNavigation').click();await page.waitForFunction(()=>document.querySelector('#headerNavigationSaveStatus').classList.contains('is-failed'));
    await page.locator('[data-header-editor-exit]').last().click();check(await page.locator('#headerEditorExitDialog').isVisible(),'Failed save remains dirty');await page.locator('#headerEditorKeepEditing').click();
    let savedPayload;
    await page.route('**/awesome_admin/header-navigation',route=>{if(route.request().method()!=='POST')return route.fallback();savedPayload=route.request().postDataJSON();return route.fulfill({status:200,contentType:'application/json',body:JSON.stringify({success:true})});});
    await page.locator('#saveHeaderNavigation').click();await page.waitForFunction(()=>document.querySelector('#headerNavigationSaveStatus').classList.contains('is-success'));
    equal(savedPayload.config_json,beforeSave,'Save payload includes all edited configuration');
    await page.locator('[data-header-editor-exit]').last().click();await page.waitForURL('**/awesome_admin');check(true,'Successful save clears dirty guard (intercepted)');
    await reload();
    for(const device of ['tablet','mobile','desktop']) {await page.locator('[data-preview-device='+device+']').click();equal(await page.locator('#deviceMode').inputValue(),device,'Preview device '+device);}
    await page.locator('[data-header-editor-exit]').first().click();await page.waitForURL('**/awesome_admin');check(true,'Device switching alone does not mark dirty');
    await reload();
    for(const [width,height] of [[1920,1080],[1440,900],[1024,768],[430,932]]) {
        await page.setViewportSize({width,height});await page.evaluate(()=>document.fonts.ready);
        const geometry=await page.evaluate(()=>{
            const root=document.querySelector('#headerNavigationEditor'),pane=root.querySelector('.mock-control-pane'),frame=root.querySelector('#previewFrame');
            return {overflow:document.documentElement.scrollWidth>innerWidth,sidebar:pane.getBoundingClientRect().width,frame:frame.getBoundingClientRect().width,frameBottom:frame.getBoundingClientRect().bottom,previewBottom:root.querySelector('.mock-preview-pane').getBoundingClientRect().bottom,fields:[...root.querySelectorAll('.header-property-row')].filter(e=>e.getBoundingClientRect().height).map(e=>({width:e.getBoundingClientRect().width,scroll:e.scrollWidth}))};
        });
        check(!geometry.overflow&&geometry.frame>0,'Viewport has no horizontal overflow '+width);
        check(geometry.frameBottom<=geometry.previewBottom+1,'Preview stays within its pane '+width);
        if(width>700)check(Math.abs(geometry.sidebar-300)<1,'Sidebar 300px at '+width);
        check(geometry.fields.every(f=>f.scroll<=f.width+1),'Property rows fit sidebar '+width);
        await shot('appearance-'+width);
    }
    await page.setViewportSize({width:1440,height:900});await reload();
    const locale=await page.locator('#headerNavigationEditor').innerText();
    check(!/\b(Sedang|mengatur|Warna|susunan|Ukuran|jarak|Simpan|perubahan|sebelum|mengikuti|discroll|Bayangan|Perilaku|Konfigurasi)\b/.test(locale),'Visible editor language is English');
    const forest=await page.evaluate(async()=>{const img=new Image();img.src='/assets/images/trees-on-forest-at-daytime-1083515.jpg';await img.decode();return img.naturalWidth>0;});
    check(forest,'Forest image decoded in runtime');
    equal(report.errors,[],'No page JavaScript errors');
    report.fields=baseline.fields.length;report.numericInputs=21;report.productionSettingsSaved=false;
    return report;
}
