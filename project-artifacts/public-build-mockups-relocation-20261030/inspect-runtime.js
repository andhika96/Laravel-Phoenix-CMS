async page => {
 const out=[];
 for(const path of ['/dashboard','/awesome_admin/themes','/awesome_admin/config','/awesome_admin/header-navigation','/home','/article','/pagebuilder-elementor/v2.4/create','/admin/file-manager-v2']){
  const requests=[];
  const handler=request=>{const url=new URL(request.url());if(url.pathname.startsWith('/build/')||url.pathname.startsWith('/mockups/'))requests.push(url.pathname);};
  page.on('request',handler);
  try{
   const response=await page.goto('https://laravel-13-phoenix.aruna'+path,{waitUntil:'domcontentloaded',timeout:25000});
   await page.waitForLoadState('networkidle',{timeout:5000}).catch(()=>{});
   const images=await page.evaluate(()=>[...document.images].map(i=>i.currentSrc||i.src).filter(src=>src.includes('/assets/images/themes/previews/')));
   if(response.status()!==200)throw new Error(path+': '+response.status());
   if(requests.length)throw new Error('Runtime still requests relocated folders: '+requests.join(','));
   out.push({path,status:response.status(),relocatedFolderRequests:requests,themePreviewImages:images});
  }finally{page.off('request',handler);}
 }
 return out;
}
