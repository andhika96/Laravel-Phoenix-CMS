import fs from 'node:fs/promises';
import path from 'node:path';
import postcss from 'postcss';
import assert from 'node:assert/strict';
const directory=path.resolve('project-artifacts/css-extension-completion-20261106');
const target='public/assets/css/shared/extending-css-bootstrap-5.css';
const original=await fs.readFile(target,'utf8');
assert.ok(!original.includes('PHOENIX BOOTSTRAP EXTENSIONS — UTILITY INDEX'),'Already completed; inspect current source before rerunning.');
const root=postcss.parse(original);
const bootstrap=postcss.parse(await fs.readFile('public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.css','utf8'));
const breakpoints={sm:576,md:768,lg:992,xl:1200,xxl:1400,'3xl':1920,'4xl':2160,'5xl':2560,'6xl':3840};
const native=new Map();bootstrap.walkRules(rule=>rule.selectors.forEach(selector=>native.set(selector.trim(),rule)));
const nativeDuplicates=new Set(['.opacity-0','.opacity-25','.opacity-75','.opacity-100','.overflow-x-hidden','.overflow-y-hidden','.align-top','.align-middle','.align-bottom']);
const removed=[],corrected=[],seen=new Set();
const normalized=value=>/^-?(?:\d+)?\.\d+$/.test(value)?String(Number(value)):value;
root.walkRules(rule=>{
 const base=rule.parent.type==='root',selector=rule.selector.trim();
 if(/^\.[wh]n-[1-5]$/.test(selector)){
  assert.ok(rule.nodes.every(node=>node.type==='decl'&&/^(width|height)$/.test(node.prop)&&parseFloat(node.value)<0));
  removed.push({selector,reason:'invalid negative dimension'});rule.remove();return;
 }
 if(base&&nativeDuplicates.has(selector)){
  for(const declaration of rule.nodes.filter(node=>node.type==='decl'))assert.ok(native.get(selector)?.nodes.some(node=>node.type==='decl'&&node.prop===declaration.prop&&normalized(node.value)===normalized(declaration.value)),selector);
  removed.push({selector,reason:'provided by bundled Bootstrap'});rule.remove();return;
 }
 const key=`${base?'base':rule.parent.params}|${selector}|${rule.nodes.filter(node=>node.type==='decl').map(node=>`${node.prop}:${node.value}:${node.important}`).join(';')}`;
 if(seen.has(key)){removed.push({selector,reason:'identical duplicate'});rule.remove();return;}seen.add(key);
 const offset=selector.match(/^\.end(?:-(?:sm|md|lg|xl|xxl|[3-6]xl))?-(\d+)$/);
 if(offset){const declaration=rule.nodes.find(node=>node.prop==='right');if(declaration.value!==`${offset[1]}%`){corrected.push(selector);declaration.value=`${offset[1]}%`;}}
 if(/^\.(position|opacity|vw|vh)-/.test(selector))rule.walkDecls(declaration=>declaration.important=true);
});
assert.equal(corrected.length,70);assert.equal(removed.filter(item=>item.reason==='invalid negative dimension').length,10);

const groups=new Map([['base',root.nodes.filter(node=>node.type==='rule')]]);
for(const [name,width] of Object.entries(breakpoints)){
 const media=root.nodes.find(node=>node.type==='atrule'&&node.name==='media'&&node.params===`(min-width: ${width}px)`);
 assert.ok(media,name);groups.set(name,media.nodes.filter(node=>node.type==='rule'));
}
const added=[];
function add(group,selector,declarations){
 const rules=groups.get(group);if(rules.some(rule=>rule.selectors.some(item=>item.trim()===selector)))return;
 if(group==='base'&&native.has(selector))return;
 const rule=postcss.rule({selector});
 for(const [prop,value] of declarations)rule.append(postcss.decl({prop,value,important:true}));
 rules.push(rule);added.push({group,selector});
}
function scalar(group,selector,property,value){add(group,selector,[[property,value]]);}
const scale={6:'3.5rem',7:'4rem',8:'4.5rem',9:'5rem',10:'6rem'};
for(const group of groups.keys()){
 const suffix=group==='base'?'':`${group}-`;
 for(const position of ['static','relative','absolute','fixed','sticky'])scalar(group,`.position-${suffix}${position}`,'position',position);
 for(const [side,property] of Object.entries({top:'top',bottom:'bottom',start:'left',end:'right'}))for(const value of [0,25,50,75,100,'auto'])scalar(group,`.${side}-${suffix}${value}`,property,value==='auto'?'auto':`${value}%`);
 for(const value of [0,25,50,75,100])scalar(group,`.opacity-${suffix}${value}`,'opacity',String(value/100));
 for(const value of [25,50,75,100])for(const [axis,property] of [['vw','width'],['vh','height']])scalar(group,`.${axis}-${suffix}${value}`,property,`${value}${axis}`);
 if(group!=='base')for(const value of ['auto',0,25,50,75,100])scalar(group,`.h-${suffix}${value}`,'height',value==='auto'?'auto':`${value}%`);
 for(const [size,value] of Object.entries(scale)){
  for(const [prefix,properties] of Object.entries({m:['margin'],mt:['margin-top'],mb:['margin-bottom'],ms:['margin-left'],me:['margin-right'],mx:['margin-left','margin-right'],my:['margin-top','margin-bottom']}))add(group,`.${prefix}-${suffix}${size}`,properties.map(property=>[property,value]));
  for(const property of ['gap','row-gap','column-gap'])scalar(group,`.${property}-${suffix}${size}`,property,value);
 }
}
scalar('base','.min-w-0','min-width','0');scalar('base','.min-h-0','min-height','0');
for(const lines of [2,3])add('base',`.line-clamp-${lines}`,[['display','-webkit-box'],['-webkit-box-orient','vertical'],['-webkit-line-clamp',String(lines)],['overflow','hidden'],['white-space','normal']]);
add('base','.dvh-100',[['height','100vh'],['height','100dvh']]);
add('base','.min-dvh-100',[['min-height','100vh'],['min-height','100dvh']]);
for(const cursor of ['pointer','grab','grabbing'])scalar('base',`.cursor-${cursor}`,'cursor',cursor);

function category(rule){
 const name=rule.selectors[0].trim();
 if(/^\.(position|start|end|top|bottom)-/.test(name))return 'POSITION & OFFSETS';
 if(/^\.(w|h|vw|vh|dvh|min-w|min-h|min-dvh)-/.test(name))return 'SIZING & VIEWPORT';
 if(/^\.opacity-/.test(name))return 'OPACITY';
 if(/^\.(?:m[trbsexy]?|p[trbsexy]?|gap|row-gap|column-gap)-/.test(name))return 'SPACING';
 if(/^\.(text|font-size|line-height|line-clamp)-/.test(name))return 'TEXT & TYPOGRAPHY';
 return 'RADIUS, OVERFLOW & CURSOR';
}
const categories=['TEXT & TYPOGRAPHY','POSITION & OFFSETS','SIZING & VIEWPORT','OPACITY','SPACING','RADIUS, OVERFLOW & CURSOR'];
function renderRule(rule,indent=''){
 const selector=rule.selectors.map(value=>value.trim()).join(`,\n${indent}`);
 return `${indent}${selector}\n${indent}{\n${rule.nodes.filter(node=>node.type==='decl').map(node=>`${indent}\t${node.prop}: ${node.value}${node.important?' !important':''};`).join('\n')}\n${indent}}\n`;
}
let output=`/*
 * PHOENIX BOOTSTRAP EXTENSIONS — UTILITY INDEX
 * Dependency: bundled Bootstrap 5.3.6_custom, loaded before this stylesheet.
 *
 * START/END sections: BASE, then SM / MD / LG / XL / XXL / 3XL / 4XL / 5XL / 6XL.
 * Each section groups text, position, sizing, opacity, spacing and small helpers.
 * Breakpoints (CSS px): sm576, md768, lg992, xl1200, xxl1400,
 *                      3xl1920, 4xl2160, 5xl2560, 6xl3840.
 * Spacing: 6=3.5rem, 7=4rem, 8=4.5rem, 9=5rem, 10=6rem.
 * Legacy w-1..5 / h-1..5 use rem: .25, .5, 1, 1.5, 3.
 * Responsive w/h percentages use 0/25/50/75/100/auto.
 * Explicit font-size helpers use px; font-size-inherit follows its parent.
 * start/end follow the bundled LTR Bootstrap left/right convention.
 * Native Bootstrap utilities are not repeated here.
 */\n`;
for(const [group,rules] of groups){
 const label=group.toUpperCase(),indent=group==='base'?'':'\t';
 output+=`\n/* ==================== START ${label} ==================== */\n`;
 if(group!=='base')output+=`@media (min-width: ${breakpoints[group]}px)\n{\n`;
 for(const type of categories){
  const members=rules.filter(rule=>category(rule)===type);if(!members.length)continue;
  output+=`\n${indent}/* START ${label} / ${type} */\n`;
  output+=members.map(rule=>renderRule(rule,indent)).join('\n');
  output+=`${indent}/* END ${label} / ${type} */\n`;
 }
 if(group!=='base')output+='}\n';
 output+=`/* ==================== END ${label} ==================== */\n`;
}
assert.doesNotThrow(()=>postcss.parse(output));
await fs.writeFile(target,output);
await fs.writeFile(path.join(directory,'transformation.json'),JSON.stringify({corrected,removed,added,beforeBytes:Buffer.byteLength(original),afterBytes:Buffer.byteLength(output)},null,2));
console.log(`Corrected ${corrected.length} offsets; removed ${removed.length} invalid/duplicate blocks; added ${added.length} utility rules.`);
