async (page) => {
  const frame = page.frames().find(f=>f.parentFrame()===page.mainFrame());
  const root = frame.locator('#ph-appearance-review');
  const files = [];
  const stamp = new Date().toISOString().replace(/[:.]/g,'-');
  for (const width of [1024,768,390]) {
    await page.setViewportSize({width,height:1200});
    await root.locator('.ap-theme[data-theme="'+(width===768?'ocean':'clean')+'"]').click();
    while (await root.locator('.ap-group[open]').count()) await root.locator('.ap-group[open]').first().locator('summary').click();
    await root.locator('[data-group="visual"]>summary').click();
    await frame.waitForFunction(()=>Math.abs(innerHeight-document.getElementById('ph-appearance-review').getBoundingClientRect().height)<3);
    const path = 'D:/Laragon/www/laravel-13-phoenix/project-artifacts/auth-appearance-layout-preview-20261106/visual-'+stamp+'-'+width+'.png';
    await page.screenshot({path,fullPage:true,scale:'css'});
    files.push(path);
  }
  return {status:'PASS',iframeContentFits:true,files};
}
