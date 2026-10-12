async page => {
  await page.setViewportSize({width:1600,height:1000});await page.evaluate(()=>window.prototypeVm.prototypeNotify('failed'));await page.locator('.ph-notice-toast.show').waitFor();
  const layers=await page.evaluate(()=>{const toast=document.querySelector('.ph-notice-toast'),box=toast.getBoundingClientRect(),top=document.elementFromPoint(box.left+20,box.top+40);return {modalZ:getComputedStyle(document.querySelector('.article-template-options-modal')).zIndex,containerZ:getComputedStyle(document.querySelector('.toast-container')).zIndex,toast:box.toJSON(),top:top?.className,opacity:getComputedStyle(toast).opacity,parent:toast.parentElement.parentElement.parentElement.outerHTML.slice(0,150)};});
  await page.evaluate(result=>{window.__noticeLayer=result;},layers);
}
