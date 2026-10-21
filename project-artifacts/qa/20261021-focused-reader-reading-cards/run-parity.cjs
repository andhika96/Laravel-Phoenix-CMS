// Run render-saved-article.php first. Uses saved HTML and the local editor build; no login required.
const {chromium}=require('playwright-core');
const fs=require('node:fs');
const path=require('node:path');
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{
  const context=await browser.newContext();const page=await context.newPage();
  const root=path.resolve(__dirname,'../../..');
  const manifest=JSON.parse(fs.readFileSync(path.join(__dirname,'saved-article-manifest.json'),'utf8'));
  const config=JSON.parse(fs.readFileSync(path.join(__dirname,'final-editor-config.json'),'utf8'));
  await page.setContent(`<html style="--ph-font-family:'${manifest.font.fontFamilyName}',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;--ph-font-size:${manifest.font.fontSize};--ph-theme-primary:#1fa675"><head><base href="http://laravel-13-phoenix.aruna/"></head><body><div id="editor"></div></body></html>`);
  await page.addStyleTag({path:path.join(root,'public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css')});
  await page.addStyleTag({url:`http://laravel-13-phoenix.aruna/storage/fonts/${manifest.font.fontFamilyCode}/fonts.css`});
  await page.addStyleTag({path:path.join(root,'public/assets/css/theme-responsive-typography.css')});
  await page.addStyleTag({path:path.join(root,'public/assets/css/article/article-editor-content.css')});
  await page.addScriptTag({path:path.join(root,'public/assets/plugins/ckeditor5/build/ckeditor.js')});
  await page.evaluate(async({config,html})=>{window.editor=await ClassicEditor.create(document.getElementById('editor'),config);editor.ui.getEditableElement().classList.add('article-rich-content');editor.setData(html)},{config,html:fs.readFileSync(path.join(__dirname,'final-editor-output.html'),'utf8')});
  const result=await require('./verify-render.cjs')(context,page);
  if(result.differences)throw Error('Editor/render mismatch');
 }finally{await browser.close()}
})().catch(error=>{console.error(error);process.exitCode=1});
