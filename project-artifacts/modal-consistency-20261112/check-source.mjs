import assert from 'node:assert/strict';
import { readFileSync, readdirSync, writeFileSync } from 'node:fs';
import path from 'node:path';
const root = process.cwd();
function files(dir) {
  return readdirSync(dir, {withFileTypes:true}).flatMap(entry => entry.isDirectory() ? files(path.join(dir, entry.name)) : entry.name.endsWith('.blade.php') ? [path.join(dir,entry.name)] : []);
}
const views = ['awesome_admin','manage_article','manage_event','manage_coverimage','workflow'].flatMap(dir=>files(path.join(root,'resources/views',dir)));
const inventory=[];
for(const file of views) {
  const source=readFileSync(file,'utf8');
  for(const match of source.matchAll(/<(?:div|dialog)\b[^>]*\bclass="([^"]*)"[^>]*>/g)) {
    const classes=match[1].split(/\s+/);
    if(classes.includes('modal-dialog') || match[0].startsWith('<dialog')) inventory.push({file:path.relative(root,file),line:source.slice(0,match.index).split('\n').length,classes});
  }
}
writeFileSync(new URL('./source-inventory.json',import.meta.url),JSON.stringify(inventory,null,2));
const missing=inventory.filter(row=>!row.classes.includes('ph-modal-dialog'));
console.log(JSON.stringify({dialogs:inventory.length,missing},null,2));
assert.deepEqual(missing,[], 'Every CMS application dialog must use the shared modal shell');
for(const [file,id,handler] of [['manage_event/manage_event.blade.php','eventDeleteModal','deleteEvent'],['manage_event/partials/form.blade.php','eventOccurrenceDeleteModal','deleteOccurrence'],['manage_event/partials/form.blade.php','eventOccurrenceCancelModal','cancelOccurrence']]) {
  const source=readFileSync(path.join(root,'resources/views',file),'utf8');
  assert.equal(source.split(`id="${id}"`).length-1,1);
  const modal=source.slice(source.indexOf(`id="${id}"`),source.indexOf('</Teleport>',source.indexOf(`id="${id}"`)));
  assert.match(modal,/modal-body pt-5 px-5 text-center/);
  assert.match(modal,new RegExp(`@click="${handler}"`));
  assert.match(modal,/data-bs-dismiss="modal"/);
}
console.log('Shared shells and Event confirmation actions verified.');
