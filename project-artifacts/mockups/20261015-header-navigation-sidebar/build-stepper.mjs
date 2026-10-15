import {readFile,writeFile} from 'node:fs/promises';
const dir=new URL('./',import.meta.url);
const html=await readFile(new URL('sidebar-drilldown.html',dir),'utf8');
const css=await readFile(new URL('stepper.css',dir),'utf8');
const script=`(()=>{const root=document.getElementById('headerNavigationEditor');root.querySelectorAll('.box-control').forEach(box=>{const link=box.querySelector('.box-link-toggle');if(link)box.querySelector('.box-control-header').append(link);});root.querySelector('[data-detail="Sizing"]').click();})();`;
await writeFile(new URL('sidebar-stepper.html',dir),html.replace('</style>',()=>css+'</style>')+'<script>'+script+'</script>');
