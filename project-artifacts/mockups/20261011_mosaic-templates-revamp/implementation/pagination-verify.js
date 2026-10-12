async page => {
  const base='http://127.0.0.1:8776/implementation/';
  const entries=await page.request.get(base+'pagination-files.json').then(response=>response.json());
  const checks=[],reference={};
  const check=(name,value)=>{if(!value)throw new Error(name);checks.push(name);};
  await page.setViewportSize({width:1440,height:1000});
  for(const entry of entries){
    await page.goto(base+entry.file);
    await page.evaluate(()=>document.fonts.ready);
    const active=page.locator('.page-item.active .page-link');
    const style=await active.evaluate(element=>{const s=getComputedStyle(element);return {height:element.getBoundingClientRect().height,border:s.borderBottomWidth,radius:s.borderRadius,background:s.backgroundColor,color:s.color,fontSize:s.fontSize};});
    check(`${entry.template} ${entry.type} uses 44px controls`,style.height===44);
    if(reference[entry.type])check(`${entry.template} ${entry.type} matches the shared model`,JSON.stringify(style)===JSON.stringify(reference[entry.type]));
    else reference[entry.type]=style;
  }
  await page.evaluate(results=>{window.__paginationParityResults=results;},{passed:checks.length,templates:[...new Set(entries.map(entry=>entry.template))],checks,reference});
}
