async page => {
  const state=await page.evaluate(()=>({key:window.prototypeVm.activeTemplateKey,surface:window.prototypeVm.surface,section:window.prototypeVm.optionsModal.section,panels:[...document.querySelectorAll('[role=tabpanel]')].map(element=>({id:element.id,text:element.innerText.slice(0,120)})),valueKeys:Object.keys(window.prototypeVm.optionsModal.value),modalError:window.prototypeVm.modalPreviewError}));
  await page.evaluate(value=>{window.__panelDiagnostic=value;},state);
}
