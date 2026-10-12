async page => {
  const errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.goto('http://127.0.0.1:8781/');await page.waitForFunction(()=>window.prototypeVm&&!window.prototypeVm.modalPreviewLoading);
  await page.locator('#prototype-template').selectOption('archive:editorial-journal');await page.waitForFunction(()=>!window.prototypeVm.modalPreviewLoading);
  await page.evaluate(()=>{const vm=window.prototypeVm;vm.setOptionsSection('editorial-journal');vm.optionsModal.value.editorial_journal.card.background.type='image';});
  await page.evaluate(()=>new Promise(resolve=>requestAnimationFrame(resolve)));
  await page.evaluate(value=>window.__runtimeErrors=value,errors);
}
