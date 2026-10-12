async page => {
  await page.goto('http://127.0.0.1:8780/');
  await page.waitForFunction(()=>window.prototypeVm&&!window.prototypeVm.modalPreviewLoading);
  await page.evaluate(()=>window.prototypeVm.prototypeNotify('success'));
  await page.waitForFunction(()=>parseFloat(getComputedStyle(document.querySelector('.ph-notice-toast')).opacity)>.95,{},{timeout:5000});
  const value=await page.locator('.ph-notice-toast').evaluate(e=>({className:e.className,opacity:getComputedStyle(e).opacity}));
  await page.evaluate(v=>window.__toastProbe=v,value);
}
