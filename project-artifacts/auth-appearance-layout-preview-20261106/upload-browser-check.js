async (page) => {
  const failures = [], browserErrors = [], writes = [], testedFields = [], screenshots = [];
  page.on('pageerror',error=>browserErrors.push(error.message));
  page.on('console',entry=>{ if (entry.type()==='error') browserErrors.push(entry.text()); });
  page.on('request',request=>{ if (request.method()!=='GET') writes.push(request.method()+' '+request.url()); });
  await page.setViewportSize({width:1024,height:2400});
  await page.goto('http://127.0.0.1:8768/');
  const frame = page.frames().find(f=>f.parentFrame()===page.mainFrame()), root = frame.locator('#ph-appearance-review');
  await root.waitFor();
  const fixture = 'D:/Laragon/www/laravel-13-phoenix/public/assets/slideshow/desktop_image_1.jpg';
  const secondFixture = 'D:/Laragon/www/laravel-13-phoenix/public/assets/slideshow/desktop_image_2.jpg';
  const oversizedFixture = 'D:/Laragon/www/laravel-13-phoenix/public/assets/images/trees-on-forest-at-daytime-1083515-cropped.png';
  const base = 'D:/Laragon/www/laravel-13-phoenix/project-artifacts/auth-appearance-layout-preview-20261106/';
  const stamp = new Date().toISOString().replace(/[:.]/g,'-');
  async function chooseTheme(theme) { await root.locator('.ap-theme[data-theme="'+theme+'"]').click(); }
  async function openGroup(group) { const box = root.locator('[data-group="'+group+'"]'); if ((await box.getAttribute('open'))===null) await box.locator('summary').click(); }
  const uploader = key=>root.locator('[data-uploader="'+key+'"]');
  async function ready(key,count=1) { await uploader(key).locator('[data-ready-image]').nth(count-1).waitFor(); }
  async function snapshot(key,label) {
    await frame.waitForFunction(()=>Math.abs(innerHeight-document.getElementById('ph-appearance-review').getBoundingClientRect().height)<3);
    const path = base+'upload-'+stamp+'-'+label+'.png';
    await uploader(key).screenshot({path,scale:'css'}); screenshots.push(path);
  }
  await root.locator('[data-page="login"]').click(); await chooseTheme('clean'); await openGroup('visual');
  if (!(await uploader('illustration').locator('[data-empty-image]').isVisible())) failures.push('Initial empty state missing');
  if (await uploader('illustration').locator('[data-preview-image],[data-remove-image]').count()) failures.push('Actions visible before upload');
  await snapshot('illustration','empty-gray');
  await uploader('illustration').locator('input').setInputFiles(fixture);
  await uploader('illustration').getByRole('progressbar').waitFor();
  const progress = Number(await uploader('illustration').getByRole('progressbar').getAttribute('aria-valuenow'));
  if (progress<0 || progress>=100) failures.push('Invalid uploading progress');
  if (await uploader('illustration').locator('[data-preview-image],[data-remove-image]').count()) failures.push('Actions appeared before completion');
  if (!(await root.locator('.ap-save').isDisabled())) failures.push('Save enabled during upload');
  await snapshot('illustration','in-progress');
  await ready('illustration'); testedFields.push('clean/illustration');
  if (!(await uploader('illustration').locator('.ap-upload-item-info').textContent()).includes('100%')) failures.push('Completion percentage missing');
  await snapshot('illustration','complete-with-actions');
  await uploader('illustration').locator('[data-preview-image]').click();
  if (!(await root.locator('dialog').isVisible())) failures.push('Image preview did not open');
  const decoded = await root.locator('dialog img').evaluate(img=>img.complete && img.naturalWidth>0);
  if (!decoded) failures.push('Preview image did not decode');
  await root.locator('[data-close-image]').click();
  await uploader('illustration').locator('input').setInputFiles({name:'broken.png',mimeType:'image/png',buffer:Buffer.from('invalid image')});
  await uploader('illustration').getByRole('alert').waitFor();
  if (await uploader('illustration').locator('[data-ready-image]').count()!==1) failures.push('Failed replacement removed the previous image');
  await uploader('illustration').locator('input').setInputFiles(oversizedFixture);
  await uploader('illustration').getByRole('alert').waitFor();
  if (!(await uploader('illustration').getByRole('alert').textContent()).includes('10 MB')) failures.push('Oversize image was not rejected');
  await root.locator('[data-page="signup"]').click(); await openGroup('visual');
  if (await uploader('illustration').locator('[data-ready-image]').count()) failures.push('Upload leaked into another page');
  await root.locator('[data-page="login"]').click(); await openGroup('visual'); await ready('illustration');
  await chooseTheme('ocean'); await chooseTheme('clean'); await openGroup('visual'); await ready('illustration');
  await uploader('illustration').locator('[data-remove-image]').click();
  if (!(await uploader('illustration').locator('[data-empty-image]').isVisible())) failures.push('Remove did not restore the gray placeholder');
  if (await uploader('illustration').locator('img,[data-preview-image],[data-remove-image]').count()) failures.push('Removed image or actions remained');
  if (await root.locator('.ap-theme[data-theme="clean"] .ap-thumb-art').getAttribute('data-image-state')!=='empty') failures.push('Theme did not use its empty image fallback');
  await snapshot('illustration','removed-gray');
  await chooseTheme('ocean'); await chooseTheme('clean'); await openGroup('visual');
  if (await uploader('illustration').locator('[data-ready-image]').count()) failures.push('Removed image returned after a theme switch');
  for (const theme of ['clean','ocean','midnight','verdant']) {
    await chooseTheme(theme); await openGroup('general');
    await uploader('logoImage').locator('input').setInputFiles(fixture); await ready('logoImage'); testedFields.push(theme+'/logoImage');
    await uploader('logoImage').locator('[data-remove-image]').click();
    if (!(await uploader('logoImage').locator('[data-empty-image]').isVisible())) failures.push(theme+' logo fallback failed');
  }
  await chooseTheme('ocean'); await openGroup('visual');
  await uploader('backgroundImage').locator('input').setInputFiles(fixture); await ready('backgroundImage'); testedFields.push('ocean/backgroundImage');
  await root.locator('#ap-field-sameImage').uncheck();
  await uploader('panelImage').locator('input').setInputFiles(fixture); await ready('panelImage'); testedFields.push('ocean/panelImage');
  await uploader('backgroundImage').locator('[data-remove-image]').click();
  if (await uploader('panelImage').locator('[data-ready-image]').count()!==1) failures.push('Background removal cleared the separate panel image');
  await uploader('panelImage').locator('[data-remove-image]').click();
  await chooseTheme('midnight'); await openGroup('information');
  await uploader('avatars').locator('input').setInputFiles([fixture,secondFixture]); await ready('avatars',2); testedFields.push('midnight/avatars');
  await uploader('avatars').locator('[data-remove-image]').first().click();
  if (await uploader('avatars').locator('[data-ready-image]').count()!==1) failures.push('Removing one avatar removed the others');
  await uploader('avatars').locator('[data-remove-image]').click();
  if (!(await uploader('avatars').locator('[data-empty-image]').isVisible())) failures.push('Last avatar removal did not restore gray');
  await chooseTheme('clean'); await openGroup('visual');
  await uploader('illustration').locator('input').setInputFiles(fixture);
  await root.locator('[data-page="reset"]').click(); await openGroup('visual');
  await uploader('illustration').locator('[data-empty-image]').waitFor();
  await page.waitForFunction(()=>true);
  await root.locator('[data-page="login"]').click(); await openGroup('visual'); await ready('illustration');
  let layoutChecks = 0;
  for (const scheme of ['light','dark']) {
    await page.emulateMedia({colorScheme:scheme,reducedMotion:'reduce'});
    for (const width of [320,390,768,820,1024]) {
      await page.setViewportSize({width,height:2400});
      const result = await frame.evaluate(()=>{
        const root=document.getElementById('ph-appearance-review'), bounds=root.querySelector('.ap-window').getBoundingClientRect(), bad=[];
        root.querySelectorAll('.ap-upload input,.ap-upload img,.ap-upload button,.ap-upload-track').forEach(el=>{
          if(!el.checkVisibility()) return; const rect=el.getBoundingClientRect();
          if(rect.left<bounds.left-1 || rect.right>bounds.right+1) bad.push(el.tagName);
        });
        return {width:innerWidth,scrollWidth:document.documentElement.scrollWidth,bad};
      });
      layoutChecks++;
      if(result.scrollWidth>result.width+1 || result.bad.length) failures.push({scheme,width,...result});
      await uploader('illustration').locator('[data-preview-image]').click();
      const dialog = await root.locator('dialog').boundingBox();
      if (dialog.width>width || dialog.x<0) failures.push('Dialog exceeded '+width+'px');
      await root.locator('[data-close-image]').click();
    }
  }
  await page.emulateMedia({colorScheme:'light'});
  await page.setViewportSize({width:390,height:2400}); await snapshot('illustration','mobile-complete');
  await uploader('illustration').locator('[data-remove-image]').click();
  await snapshot('illustration','mobile-removed-gray');
  await page.setViewportSize({width:1024,height:2400});
  if (browserErrors.length || writes.length) failures.push({browserErrors,writes});
  if (failures.length) throw new Error(JSON.stringify(failures));
  return {status:'PASS',testedFields,layoutChecks,checks:['initial gray placeholder','automatic progress below 100','actions only after completion','preview decodes selected image','failed replacement keeps previous image','oversize rejected','page/theme isolation','remove clears image and actions','separate photo fields remain independent','multiple avatar removal','upload completes across page switches','mobile preview dialog'],browserErrors,writes,screenshots};
}
