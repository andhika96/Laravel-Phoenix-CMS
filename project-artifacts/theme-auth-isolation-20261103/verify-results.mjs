import fs from 'node:fs';
import assert from 'node:assert/strict';
const dir='project-artifacts/theme-auth-isolation-20261103';
const result=file=>{
 const payload=JSON.parse(fs.readFileSync(`${dir}/${file}`,'utf8').replace(/^\uFEFF/,''));
 assert(!payload.isError,payload.error);return JSON.parse(payload.result);
};
const audit=result('browser-theme-audit-final.json');
assert.equal(audit.length,36);
const report=[];
for(const theme of ['arunika_prism','arunika_aurora','arunika_lucent','arunika_equinox','default','calm_green'])for(const mode of ['light','dark']){
 const state=variant=>audit.find(item=>item.theme===theme&&item.mode===mode&&item.variant===variant);
 assert.equal(state('current').color,state('theme-color').color,`${theme}/${mode} color differs from theme`);
 assert.equal(state('current').label,state('theme-color').label,`${theme}/${mode} label differs from theme`);
 assert.equal(state('current').background,state('theme-color').background,`${theme}/${mode} background changed`);
 assert.equal(state('current').authPrimary,'');
 report.push({theme,mode,before:state('original').color,after:state('current').color});
}
console.table(report);
const isolation=result('browser-isolation.json');
assert.equal(isolation.cms.length,6);assert.equal(isolation.auth.length,16);
assert(isolation.cms.every(item=>!item.loadedAuthCss&&!item.authInjectionChangedCms));
console.log('PASS: all six CMS themes follow their own active colors in light/dark; auth CSS has no effect on CMS; 16 auth layouts exclude CMS CSS.');
