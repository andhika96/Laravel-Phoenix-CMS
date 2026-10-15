async page => {
 const checks=[];const check=(name,value)=>{if(!value)throw Error(name);checks.push(name)};
 await page.setViewportSize({width:1440,height:900});await page.goto('http://127.0.0.1:8781/');await page.waitForFunction(()=>window.prototypeVm&&!window.prototypeVm.modalPreviewLoading);
 await page.locator('.article-template-header-field input.form-control').first().fill('Changed');
 await page.getByRole('button',{name:'Cancel',exact:true}).click();
 const dialog=page.locator('dialog[open]');await dialog.waitFor();
 check('One dialog, no duplicate toast',await page.locator('dialog[open]').count()===1&&await page.locator('.toast.show').count()===0);
 check('No footer warning',await page.locator('.article-template-options-dismiss').count()===0);
 check('Safe action focused',await dialog.getByRole('button',{name:'Keep editing'}).evaluate(e=>e===document.activeElement));
 await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261013-single-discard-dialog/desktop.png'});
 await page.keyboard.press('Escape');await page.waitForFunction(()=>!document.querySelector('dialog').open);
 check('Escape preserves draft and parent modal',await page.evaluate(()=>window.prototypeVm.optionsModal.dirty&&document.querySelector('#modalArticleTemplateOptions').classList.contains('show')));
 await page.setViewportSize({width:390,height:844});await page.getByRole('button',{name:'Cancel',exact:true}).click();await dialog.waitFor();
 check('Mobile dialog fits viewport',await dialog.evaluate(e=>{const b=e.getBoundingClientRect();return b.left>=0&&b.right<=innerWidth&&b.bottom<=innerHeight}));
 await page.screenshot({path:'D:/Laragon/www/laravel-13-phoenix/project-artifacts/implementations/20261013-single-discard-dialog/mobile.png'});
 await dialog.getByRole('button',{name:'Keep editing'}).click();await page.waitForFunction(()=>!document.querySelector('dialog').open);
 await page.getByRole('button',{name:'Cancel',exact:true}).click();await dialog.getByRole('button',{name:'Discard changes'}).click();await page.waitForFunction(()=>!window.prototypeVm.optionsModal.value);
 check('Discard closes both dialogs',await page.locator('dialog[open]').count()===0);
 await page.evaluate(value=>window.__discardResults=value,{passed:checks.length,checks});
}
