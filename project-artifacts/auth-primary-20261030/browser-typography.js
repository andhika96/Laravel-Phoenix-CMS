async page => {
 await page.reload();
 await page.locator('#authPrimaryColor-login').waitFor();
 const result=[];
 for(const [width,height] of [[1280,720],[1920,1080],[390,844]]){
  await page.setViewportSize({width,height});
  const item=await page.evaluate(()=>({width:innerWidth,body:getComputedStyle(document.body).fontSize,labels:[...document.querySelectorAll('#ph-form-config-appearance .form-label.fw-bold')].slice(0,4).map(n=>({tag:n.tagName,text:n.textContent.trim(),size:getComputedStyle(n).fontSize,weight:getComputedStyle(n).fontWeight,family:getComputedStyle(n).fontFamily}))}));
  if(item.labels.length!==4||new Set(item.labels.map(n=>n.size)).size!==1)throw Error('Labels differ: '+JSON.stringify(item));
  if(new Set(item.labels.map(n=>n.family)).size!==1)throw Error('Label families differ');
  result.push(item);
  await page.screenshot({path:`appearance-labels-${width}.png`,fullPage:true});
 }
 return result;
}
