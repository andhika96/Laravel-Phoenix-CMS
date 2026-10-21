const { chromium }=require('playwright-core');
const fs=require('node:fs');
const path=require('node:path');
const assert=require('node:assert/strict');
const root=path.resolve(__dirname,'../../..');
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try {
  const page=await browser.newPage(); const errors=[]; const results=[];
  page.on('pageerror',e=>errors.push(e.message));
  await page.route('**/*',route=>{
   const url=new URL(route.request().url());
   if(url.pathname.endsWith('alpine-lake.png')) return route.fulfill({path:path.join(root,'project-artifacts/mockups/20261011_mosaic-templates-revamp/assets/images/alpine-lake.png'),contentType:'image/png'});
   const target=path.resolve(root,'public','.'+url.pathname);
   if(target.startsWith(path.join(root,'public')+path.sep)&&fs.existsSync(target)&&fs.statSync(target).isFile()) return route.fulfill({path:target});
   return route.fulfill({status:404,body:''});
  });
  async function load(name){
   await page.setContent('<html><head><meta name="viewport" content="width=device-width,initial-scale=1"><base href="http://focused-reader.test/"></head><body>'+fs.readFileSync(path.join(__dirname,name),'utf8')+'</body></html>');
   await page.addStyleTag({path:path.join(root,'public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css')});
   await page.addStyleTag({path:path.join(root,'public/assets/css/article/article-frontend-2026.css')});
   await page.addStyleTag({content:'body{margin:0;font-family:Arial,sans-serif}'});
  }
  for(const name of ['runtime-default.html','runtime-stress.html','runtime-custom.html']){
   for(const width of [320,390,576,768,834,1024,1440]){
    await page.setViewportSize({width,height:1000}); await load(name);
    const data=await page.evaluate(()=>({overflow:document.documentElement.scrollWidth>innerWidth,title:getComputedStyle(document.querySelector('h1')).fontSize,alignment:getComputedStyle(document.querySelector('header')).textAlign,body:getComputedStyle(document.querySelector('.article-rich-content')).fontSize,ratio:document.querySelector('.article-detail__cover')?getComputedStyle(document.querySelector('.article-detail__cover')).aspectRatio:null}));
    assert.equal(data.overflow,false,name+' '+width+' overflow');
    if(name==='runtime-default.html'){
     assert.equal(data.title,width<576?'32px':width<992?'40px':'48px');
     if([390,834,1440].includes(width))await page.screenshot({path:path.join(__dirname,`runtime-${width}.png`),fullPage:true});
    }
    results.push({name,width,...data});
   }
  }
  await page.setViewportSize({width:390,height:900});
  await page.setContent('<div id="ph-app-manage-article-templates"></div><div id="reader-settings"></div>');
  await page.addStyleTag({path:path.join(root,'public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css')});
  await page.addStyleTag({path:path.join(root,'public/assets/css/article/article-template-manager-2026.css')});
  await page.addScriptTag({path:path.join(root,'node_modules/vue/dist/vue.global.js')});
  await page.evaluate(()=>{window.realCreateApp=Vue.createApp;Vue.createApp=options=>{window.managerOptions=options;return {mount(){return options}}}});
  await page.addScriptTag({path:path.join(root,'public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js')});
  await page.evaluate(({html,options})=>{
   Vue.createApp=window.realCreateApp;
   const original=window.managerOptions;
   window.readerVM=Vue.createApp({...original,mounted(){},beforeUnmount(){},watch:{},template:html,data(){return {...original.data(),surface:'detail',optionsDevice:'desktop',draft:{detail_template:'focused-reader',detail_template_options:{'focused-reader':structuredClone(options)}},optionsModal:{key:'focused-reader',surface:'detail',section:'focused-reader',value:structuredClone(options),dirty:true}}},methods:{...original.methods,showNotice(){},closeTemplateOptions(){},fitOptionsPreview(){},scheduleModalPreview(){}}}).mount('#reader-settings');
  },{html:fs.readFileSync(path.join(__dirname,'runtime-settings.html'),'utf8'),options:JSON.parse(fs.readFileSync(path.join(__dirname,'runtime-options.json'),'utf8'))});
  await page.getByRole('button',{name:'mobile',exact:true}).click();
  await page.getByLabel('Title size (px)',{exact:true}).fill('34');
  await page.getByLabel('Header alignment',{exact:false}).selectOption('center');
  await page.getByLabel('Show cover',{exact:true}).uncheck();
  assert.equal(await page.evaluate(()=>window.readerVM.optionsModal.value.focused_reader.title_size.mobile),34);
  assert.equal(await page.evaluate(()=>window.readerVM.optionsModal.value.focused_reader.title_size.desktop),48);
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false,'settings mobile overflow');
  await page.screenshot({path:path.join(__dirname,'runtime-settings-mobile.png'),fullPage:true});
  const applied=await page.evaluate(()=>{window.readerVM.applyTemplateOptions();return {options:window.readerVM.draft.detail_template_options['focused-reader'],url:window.readerVM.previewUrl,sections:window.readerVM.optionSections.map(x=>x.key)}});
  assert.equal(applied.options.focused_reader.title_size.mobile,34);
  assert.equal(applied.options.focused_reader.cover_enabled,false);
  assert.ok(applied.sections.includes('focused-reader'));
  assert.ok(applied.url.includes('template_options='));
  assert.ok(applied.url.length<8000,'preview GET length '+applied.url.length);
  assert.deepEqual(errors,[]);
  fs.writeFileSync(path.join(__dirname,'runtime-verification.json'),JSON.stringify({results,settings:'Vue bindings and Apply passed',previewUrlLength:applied.url.length,pageErrors:errors},null,2));
  console.log(JSON.stringify({responsiveChecks:results.length,settings:'passed',previewUrlLength:applied.url.length,pageErrors:errors}));
 }finally{await browser.close()}
})().catch(e=>{console.error(e);process.exitCode=1});
