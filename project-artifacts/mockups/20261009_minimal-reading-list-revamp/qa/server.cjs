const http=require('node:http');
const fs=require('node:fs');
const path=require('node:path');
const root=path.resolve(__dirname,'..');
const types={'.html':'text/html; charset=utf-8','.css':'text/css; charset=utf-8','.js':'application/javascript; charset=utf-8','.png':'image/png','.woff2':'font/woff2','.ttf':'font/ttf'};
http.createServer((request,response)=>{
  let file;
  try{file=path.resolve(root,'.'+decodeURIComponent(new URL(request.url,'http://127.0.0.1').pathname));}
  catch{response.writeHead(400).end();return;}
  if(file!==root&&!file.startsWith(root+path.sep)){response.writeHead(403).end();return;}
  if(file===root)file=path.join(root,'preview.html');
  fs.stat(file,(error,stat)=>{
    if(error||!stat.isFile()){response.writeHead(404).end();return;}
    response.writeHead(200,{'Content-Type':types[path.extname(file)]||'application/octet-stream','Content-Length':stat.size,'Cache-Control':'no-cache'});
    fs.createReadStream(file).on('error',()=>response.destroy()).pipe(response);
  });
}).listen(8774,'127.0.0.1',()=>console.log('Preview: http://127.0.0.1:8774/preview.html'));
