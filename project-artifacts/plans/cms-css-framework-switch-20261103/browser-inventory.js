async page => {
 const rows=[];const errors=[];
 page.on('pageerror',error=>errors.push({url:page.url().split('?')[0],message:error.message}));
 for(const route of ['/dashboard','/manage_article','/manage_coverimage','/manage_event','/awesome_admin/user','/awesome_admin/role','/awesome_admin/permission','/awesome_admin/smtp','/awesome_admin/config','/awesome_admin/appearance','/awesome_admin/themes','/workflow']){
  const response=await page.goto('https://laravel-13-phoenix.aruna'+route,{waitUntil:'domcontentloaded'});
  await page.waitForTimeout(250);
  rows.push(await page.evaluate(({route,status})=>{
   const count=selector=>document.querySelectorAll(selector).length;
   return {route,status,finalPath:location.pathname,theme:document.body.className,modalDefinitions:count('.modal'),nativeDialogs:count('dialog'),dropdownMenus:count('.dropdown-menu'),pagination:count('.pagination'),tables:count('table'),toasts:count('.toast'),tabs:count('[data-bs-toggle=tab]'),toggles:[...new Set([...document.querySelectorAll('[data-bs-toggle]')].map(n=>n.dataset.bsToggle))],vueVersion:window.Vue?.version,bootstrapLoaded:typeof window.bootstrap!=='undefined',tailwindStylesLoaded:[...document.querySelectorAll('link[rel=stylesheet]')].some(n=>/tailwind/i.test(n.href)),plugins:{coloris:typeof window.Coloris!=='undefined',vuePaginate:typeof window.VuejsPaginateNext!=='undefined',vueSelect:typeof window['vue-select']!=='undefined',datepicker:typeof window.VueDatePicker!=='undefined',echarts:typeof window.echarts!=='undefined',jsVectorMap:typeof window.jsVectorMap!=='undefined'},scriptFiles:[...document.scripts].filter(n=>n.src.startsWith(location.origin)&&n.src.includes('/assets/js/')).map(n=>n.src.split('?')[0].replace(location.origin,''))};
  },{route,status:response?.status()}));
 }
 return {pages:rows,errors,note:'Read-only page load inventory; no component actions, writes, CRUD submissions, or migration prototype.'};
}
