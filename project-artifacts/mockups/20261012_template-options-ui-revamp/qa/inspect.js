async page => {
  const failures=[];page.on('pageerror',error=>failures.push(error.message));page.on('response',response=>{if(response.status()>=400)failures.push(`${response.status()} ${response.url()}`);});
  await page.setViewportSize({width:1600,height:1000});await page.goto('http://127.0.0.1:8780/');await page.waitForSelector('.article-template-options-modal.show');await page.waitForFunction(()=>window.prototypeVm&&!window.prototypeVm.modalPreviewLoading);await page.evaluate(()=>document.fonts.ready);
  await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20261012_template-options-ui-revamp/qa/header-first.png'});
  const metrics=await page.evaluate(()=>({title:document.querySelector('.modal-title').textContent,sections:window.prototypeVm.optionSections.map(section=>section.key),inputs:[...document.querySelectorAll('.article-template-options-panel .form-control')].map(element=>({type:element.type,height:element.getBoundingClientRect().height,font:getComputedStyle(element).fontSize,rows:element.rows})),error:window.prototypeVm.modalPreviewError}));
  await page.evaluate(result=>{window.__inspection=result;},{failures,metrics});
}
