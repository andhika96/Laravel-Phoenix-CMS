import {readFile,writeFile} from 'node:fs/promises';
const dir=new URL('./',import.meta.url);
const original=await readFile(new URL('sidebar-preview.html',dir),'utf8');
const css=await readFile(new URL('drilldown.css',dir),'utf8');const js=await readFile(new URL('drilldown.js',dir),'utf8');
await writeFile(new URL('sidebar-drilldown.html',dir),original.replace('</style>',()=>css+'</style>')+'<script>'+js+'</script>');
