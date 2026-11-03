import fs from 'node:fs';
import path from 'node:path';
const root=process.cwd();
const output='project-artifacts/plans/cms-css-framework-switch-20261103';
const walk=dir=>fs.readdirSync(dir,{withFileTypes:true}).flatMap(entry=>entry.isDirectory()?walk(path.join(dir,entry.name)):entry.name.endsWith('.blade.php')&&!entry.name.includes('.bak')?[path.join(dir,entry.name).replaceAll('\\','/')]:[]);
const allViews=walk('resources/views');
const excluded=/\/(auth|emails|setup|article|homepage|event|filemanager_v2|pagebuilder[^/]*)\//;
const selected=new Set(allViews.filter(file=>!excluded.test(file)&&/custom_theme\(\s*['"]cms['"]\s*\)/.test(fs.readFileSync(file,'utf8'))));
const themes=['default','calm_green','arunika_prism','arunika_aurora','arunika_lucent','arunika_equinox'];
for(const theme of themes){selected.add(`resources/views/themes/${theme}/cms/cms_layout.blade.php`);selected.add(`resources/views/themes/${theme}/components/menu.blade.php`);}
for(let previous=-1;previous!==selected.size;){
 previous=selected.size;
 for(const file of [...selected]){
  const source=fs.readFileSync(file,'utf8');
  for(const match of source.matchAll(/@(?:include|includeIf|includeWhen|component)\(\s*['"]([^'"]+)['"]/g)){
   const view='resources/views/'+match[1].replaceAll('.','/')+'.blade.php';
   if(fs.existsSync(view)&&!excluded.test(view))selected.add(view);
  }
 }
}
const seeds=[...selected];
const scripts=new Set();
for(const file of seeds){
 const source=fs.readFileSync(file,'utf8');
 for(const match of source.matchAll(/assets\/js\/[A-Za-z0-9_\-./]+\.js/g)){
  const script='public/'+match[0];
  if(fs.existsSync(script)&&!/(pagebuilder|filemanager|auth|frontend)/i.test(script))scripts.add(script);
 }
}
const files=[...selected,...scripts].sort();
const categories={
 'Modal / confirmation':/\bbootstrap\.Modal\b|data-bs-toggle\s*=\s*["']modal|\bmodal-dialog\b/g,
 'Native dialog':/<dialog\b|\.showModal\(\)/g,
 'Dropdown / action menu':/\bbootstrap\.Dropdown\b|data-bs-toggle\s*=\s*["']dropdown|\bdropdown-menu\b/g,
 'Collapse / accordion':/\bbootstrap\.Collapse\b|data-bs-toggle\s*=\s*["']collapse|\baccordion-button\b/g,
 'Toast / feedback':/\bbootstrap\.Toast\b|\bph-notice-toast\b/g,
 'Tooltip':/\bbootstrap\.Tooltip\b|data-bs-toggle\s*=\s*["']tooltip/g,
 'Popover':/\bbootstrap\.Popover\b|data-bs-toggle\s*=\s*["']popover/g,
 'Offcanvas / drawer':/\bbootstrap\.Offcanvas\b|data-bs-toggle\s*=\s*["']offcanvas|\boffcanvas-(?:body|header)\b/g,
 'Tabs':/\bbootstrap\.Tab\b|data-bs-toggle\s*=\s*["'](?:tab|pill)|\bnav-tabs\b/g,
 'Carousel':/\bbootstrap\.Carousel\b|\bcarousel-item\b/g,
 'Pagination':/\bph-pagination\b|\bpage-link\b|\bpaginationRange\b/g,
 'Table / responsive list':/\btable-responsive\b|<table\b/g,
 'Form / input group':/\bform-control\b|\bform-select\b|\binput-group\b/g,
 'Checkbox / radio / switch':/\bform-check\b|\bform-switch\b|\bform-check-input\b/g,
 'Validation':/\binvalid-feedback\b|\bvalid-feedback\b|\bis-invalid\b|\bis-valid\b/g,
 'Loading / placeholder / progress':/\bspinner-border\b|\bplaceholder-glow\b|\bprogress-bar\b/g,
 'Buttons / badge / alert / card':/\bbtn-primary\b|\bph-btn-theme\b|\bbadge\b|\balert-(?:warning|danger|success|info)\b|\bcard-body\b/g,
 'Sidebar / navigation':/\bph-sidebar\b|\barv7-sidebar\b/g,
 'Breadcrumb':/\bbreadcrumb\b|Breadcrumbs::/g,
};
const plugins={
 Coloris:/Coloris|coloris/g,
 'Vue Select':/vue-select|v-select|VueNextSelect/g,
 'CKEditor':/ckeditor|CKEditor|ClassicEditor/g,
 'CKFinder media picker':/CKFinder|ckfinder/g,
 'SimpleBar':/SimpleBar|simplebar/g,
 'Charts':/echarts|apexcharts|chart\.js|new Chart\(/gi,
 'Maps':/leaflet|jsvectormap|L\.map\(/gi,
 'Sortable / drag-drop':/Sortable|sortablejs|dragstart|draggable/g,
 'Date/time picker':/flatpickr|datepicker|datetimepicker/g,
 'Crop / upload library':/\bCropper\b|\bFilePond\b|filepond(?:\.|\/)|dropzone(?:\.min)?\.js|new Dropzone/gi,
 'Code editor':/monaco|codemirror|ace\.edit/gi,
 'Select2':/select2/gi,
 'SweetAlert':/sweetalert|Swal\./gi,
};
const scan=patterns=>Object.fromEntries(Object.entries(patterns).map(([name,pattern])=>{
 const hits=[];
 for(const file of files){
  fs.readFileSync(file,'utf8').split(/\r?\n/).forEach((line,index)=>{pattern.lastIndex=0;if(pattern.test(line))hits.push({file,line:index+1,excerpt:line.trim().slice(0,240)});});
 }
 return [name,{files:new Set(hits.map(item=>item.file)).size,matchingLines:hits.length,evidence:hits.slice(0,12)}];
}));
const api=[];const events=[];const toggles=[];
for(const file of files){const source=fs.readFileSync(file,'utf8');
 for(const match of source.matchAll(/bootstrap\.(Modal|Toast|Tooltip|Popover|Dropdown|Collapse|Offcanvas|Tab|Carousel|Alert|Button|ScrollSpy)\b/g))api.push({file,component:match[1],line:source.slice(0,match.index).split('\n').length});
 for(const match of source.matchAll(/\b(?:show|shown|hide|hidden|inserted|slide|slid|close|closed|activate)\.bs\.[a-z]+/g))events.push({file,event:match[0],line:source.slice(0,match.index).split('\n').length});
 for(const match of source.matchAll(/data-bs-toggle\s*=\s*["']([a-z]+)/g))toggles.push({file,component:match[1],line:source.slice(0,match.index).split('\n').length});
}
const result={scope:'Candidate active CMS views and their literal JS references; not a runtime reachability proof',themes,viewCount:selected.size,scriptCount:scripts.size,files,categories:scan(categories),plugins:scan(plugins),bootstrapApi:api,bootstrapEvents:events,dataToggles:toggles};
fs.mkdirSync(output,{recursive:true});
fs.writeFileSync(output+'/component-audit.json',JSON.stringify(result,null,2));
fs.writeFileSync(output+'/source-files.txt',files.join('\n')+'\n');
console.log(`Candidate CMS scope: ${result.viewCount} views, ${result.scriptCount} scripts.`);
console.table(Object.entries(result.categories).map(([component,data])=>({component,files:data.files,matchingLines:data.matchingLines})));
console.table(Object.entries(result.plugins).filter(([,data])=>data.files).map(([plugin,data])=>({plugin,files:data.files})));
console.log('Bootstrap APIs:',[...new Set(api.map(item=>item.component))].join(', '));
console.log('Bootstrap events:',[...new Set(events.map(item=>item.event))].join(', '));
