async page => {
  const base='http://127.0.0.1:8780/';
  const output='D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261012_template-options-ui-revamp/qa/';
  const checks=[],failures=[],panels=[];
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  page.on('pageerror',error=>failures.push(error.message));
  page.on('response',response=>{if(response.status()>=400)failures.push(`${response.status()} ${response.url()}`);});
  await page.setViewportSize({width:1600,height:1000});await page.goto(base);await page.waitForSelector('.article-template-options-modal.show');await page.waitForFunction(()=>window.prototypeVm&&!window.prototypeVm.modalPreviewLoading);
  await page.evaluate(()=>document.fonts.ready);
  check('Description uses three rows in revamp',await page.locator('textarea').first().evaluate(element=>element.rows===3));
  check('Header controls use the CMS 36px scale',await page.locator('.article-template-options-panel input.form-control').first().evaluate(element=>element.getBoundingClientRect().height===36));
  check('Footer is fully inside desktop viewport',await page.locator('.modal-footer').evaluate(element=>element.getBoundingClientRect().bottom<=innerHeight));
  await page.screenshot({path:output+'header-desktop.png'});
  await page.getByRole('button',{name:'Saat ini',exact:true}).click();
  check('Current comparison preserves the ten-row source textarea',await page.locator('textarea').first().evaluate(element=>element.rows===10));
  await page.getByRole('button',{name:'Revamp',exact:true}).click();
  check('Revamp comparison restores the three-row textarea',await page.locator('textarea').first().evaluate(element=>element.rows===3));
  const templates=await page.locator('#prototype-template option').evaluateAll(options=>options.map(option=>({value:option.value,label:option.textContent})));
  check('All five archive and three detail templates are available',templates.length===8);
  for(const template of templates){
    await page.locator('#prototype-template').selectOption(template.value);
    await page.waitForFunction(value=>window.prototypeVm.optionsModal.key===value.split(':')[1]&&!window.prototypeVm.modalPreviewLoading,template.value);
    check(`${template.label} live source preview loads`,await page.evaluate(()=>window.prototypeVm.modalPreviewError===''));
    const sections=await page.evaluate(()=>window.prototypeVm.optionSections.map(section=>({key:section.key,label:section.label})));
    for(const section of sections){
      await page.evaluate(key=>window.prototypeVm.setOptionsSection(key),section.key);
      await page.evaluate(key=>{const value=window.prototypeVm.optionsModal.value;
        if(key==='toolbar'){value.toolbar.search.enabled=true;value.toolbar.category.enabled=true;}
        if(key==='thumbnail')value.thumbnail.frame.enabled=true;
        if(key==='pagination'){value.pagination.frame.enabled=true;value.pagination.padding.enabled=true;value.pagination.margin.enabled=true;}
        if(key==='shell'){value.shell.frame.enabled=true;value.shell.padding.enabled=true;value.shell.margin.enabled=true;}
        if(key==='editorial-journal'){value.editorial_journal.read_more.enabled=true;value.editorial_journal.card.height.mode='fixed';value.editorial_journal.card.background.type='image';}
      },section.key);
      await page.locator('#article-template-option-panel-'+section.key).waitFor({state:'visible'});
      const state=await page.locator('.article-template-options-panel').evaluate(panel=>{const box=panel.getBoundingClientRect(),controls=[...panel.querySelectorAll('input:not([type=checkbox]),select,textarea')].filter(element=>element.getBoundingClientRect().width>0);return {overflow:panel.scrollWidth>panel.clientWidth+1,native:controls.every(element=>['INPUT','SELECT','TEXTAREA'].includes(element.tagName)),small:controls.filter(element=>parseFloat(getComputedStyle(element).fontSize)<13).map(element=>element.className),outside:controls.filter(element=>element.getBoundingClientRect().right>box.right+1).map(element=>element.className)};});
      check(`${template.label} / ${section.label} has no horizontal overflow`,!state.overflow&&state.outside.length===0);
      check(`${template.label} / ${section.label} retains native form controls`,state.native);
      check(`${template.label} / ${section.label} avoids nested small field fonts`,state.small.length===0);
      panels.push({template:template.value,section:section.key});
      if(template.value==='archive:editorial-journal'&&section.key==='editorial-journal')await page.screenshot({path:output+'editorial-controls.png'});
      if(template.value==='archive:minimal-reading-list'&&section.key==='pagination')await page.screenshot({path:output+'pagination-controls.png'});
    }
  }
  await page.locator('#prototype-template').selectOption('archive:minimal-reading-list');await page.waitForFunction(()=>!window.prototypeVm.modalPreviewLoading);
  const header=page.locator('.article-template-header-field');
  await header.nth(1).locator('input.form-control').fill('Reading journal preview');
  await page.waitForFunction(()=>window.prototypeVm.optionsModal.dirty&&!window.prototypeVm.modalPreviewLoading);
  await page.waitForFunction(()=>document.querySelector('iframe').contentDocument.querySelector('h1')?.textContent==='Reading journal preview');
  check('Editing existing title updates the actual Blade preview',true);
  await page.getByRole('button',{name:'Cancel',exact:true}).click();
  check('Dirty draft keeps the existing dismissal warning',await page.locator('.article-template-options-dismiss').isVisible());
  check('Dismissal warning uses the CMS warning callout',await page.locator('.article-template-options-dismiss.ph-callout-warning').count()===1);
  await page.getByRole('button',{name:'Keep editing',exact:true}).click();
  for(const [notice,callout] of [['success','success'],['warning','warning'],['failed','danger']]){
    await page.locator('#prototype-notice').selectOption(notice);await page.locator('.ph-notice-toast.show').waitFor();
    await page.waitForFunction(()=>parseFloat(getComputedStyle(document.querySelector('.ph-notice-toast')).opacity)>.95);
    check(`${notice} uses the Add Article CMS toast and callout`,await page.locator('.ph-notice .ph-notice-toast.ph-callout-no-border.ph-callout-'+callout).count()===1);
    check(`${notice} retains the standard CMS accent strip`,await page.locator('.ph-notice-toast').evaluate(element=>getComputedStyle(element,'::before').height==='4px'));
    check(`${notice} notice has alert semantics and a close action`,await page.locator('.ph-notice-toast[role=alert] [data-bs-dismiss=toast]').count()===1);
    check(`${notice} notice is visually above the modal`,await page.locator('.ph-notice-toast').evaluate(element=>{const box=element.getBoundingClientRect();return document.elementFromPoint(box.left+20,box.top+40)?.closest('.ph-notice-toast')===element;}));
    await page.screenshot({path:output+'notice-'+notice+'.png'});
    await page.evaluate(status=>window.prototypeVm.prototypeNotify(status),notice);
    await page.locator('.ph-notice-toast.show').waitFor();
    await page.locator('.ph-notice-toast [data-bs-dismiss=toast]').click();await page.waitForFunction(()=>!document.querySelector('.ph-notice-toast').classList.contains('showing')&&!document.querySelector('.ph-notice-toast').classList.contains('show'));
  }
  await page.locator('#prototype-template').selectOption('archive:editorial-journal');await page.waitForFunction(()=>!window.prototypeVm.modalPreviewLoading);
  await page.evaluate(()=>window.prototypeVm.setOptionsSection('editorial-journal'));
  await page.locator('.article-template-editorial-journal-group').last().scrollIntoViewIfNeeded();
  check('Last form controls can be scrolled above the footer',await page.locator('.article-template-editorial-journal-group').last().evaluate(element=>element.getBoundingClientRect().top<document.querySelector('.modal-footer').getBoundingClientRect().top));
  await page.locator('#prototype-template').selectOption('archive:minimal-reading-list');await page.waitForFunction(()=>!window.prototypeVm.modalPreviewLoading);
  for(const width of [1280,1024,768,390,375,320]){
    await page.setViewportSize({width,height:900});
    check(`Modal and footer fit at ${width}px`,await page.locator('.modal-footer').evaluate(element=>element.getBoundingClientRect().bottom<=innerHeight&&document.documentElement.scrollWidth<=innerWidth));
    if(width<=767){
      check(`Mobile section picker is visible at ${width}px`,await page.locator('.article-template-options-section-picker').isVisible());
      check(`Mobile inputs are readable at ${width}px`,await page.locator('.article-template-options-panel input.form-control').first().evaluate(element=>parseFloat(getComputedStyle(element).fontSize)>=16));
      await page.locator('.article-template-options-section-picker select').selectOption('pagination');
      check(`Radius and unit fields fit at ${width}px`,await page.locator('.article-template-options-panel').evaluate(element=>element.scrollWidth<=element.clientWidth+1));
      await page.locator('.article-template-options-section-picker select').selectOption('header');
    }
    if(width===390)await page.screenshot({path:output+'mobile-settings.png'});
    if(width===768)await page.screenshot({path:output+'tablet-settings.png'});
  }
  await page.setViewportSize({width:390,height:900});await page.getByRole('button',{name:'Preview',exact:true}).click();
  check('Mobile preview tab remains functional',await page.locator('.article-template-options-preview').isVisible());await page.screenshot({path:output+'mobile-preview.png'});
  await page.evaluate(results=>{window.__optionsUiResults=results;},{passed:checks.length,checks,panels,failures});
  check('All fixture requests and Vue interactions succeeded: '+JSON.stringify(failures),failures.length===0);
  await page.evaluate(results=>{window.__optionsUiResults=results;},{passed:checks.length,checks,panels,failures});
}

