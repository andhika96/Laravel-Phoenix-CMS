import {readFile,writeFile} from 'node:fs/promises';
const dir=new URL('./',import.meta.url);
const base=await readFile(new URL('sidebar-stepper.html',dir),'utf8');
const css=await readFile(new URL('horizontal-stepper.css',dir),'utf8');
await writeFile(new URL('sidebar-horizontal-stepper.html',dir),base.replace('</style>',()=>css+'</style>'));
