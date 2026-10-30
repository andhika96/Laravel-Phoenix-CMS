async (page) => {
    const out=[];
    for (const path of ['/dashboard','/account']) {
        const response=await page.goto('https://laravel-13-phoenix.aruna'+path,{waitUntil:'networkidle',timeout:25000});
        out.push(await page.evaluate(({path,status})=>({path,status,resources:performance.getEntriesByType('resource').filter(r=>r.name.includes('/assets/css/')).map(r=>({url:r.name,status:r.responseStatus,transferSize:r.transferSize,decodedBodySize:r.decodedBodySize}))}),{path,status:response.status()}));
    }
    return out;
}
