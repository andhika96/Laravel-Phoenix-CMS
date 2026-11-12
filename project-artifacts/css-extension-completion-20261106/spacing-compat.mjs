import fs from 'node:fs/promises';
import postcss from 'postcss';
import assert from 'node:assert/strict';
const target='public/assets/css/shared/extending-css-bootstrap-5.css';
const root=postcss.parse(await fs.readFile(target,'utf8'));
const bootstrap=postcss.parse(await fs.readFile('public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.css','utf8'));
const breakpoints={sm:576,md:768,lg:992,xl:1200,xxl:1400,'3xl':1920,'4xl':2160,'5xl':2560,'6xl':3840};
const prefixes=['m','mx','my','mt','me','mb','ms','p','px','py','pt','pe','pb','ps','gap','row-gap','column-gap'];
const log=[];
function info(rule,name){
 const suffix=name==='base'?'':`${name}-`;
 const match=rule.selectors[0].trim().match(new RegExp(`^\\.(${prefixes.join('|')})-${suffix}(n?[0-9]+|auto)$`));
 if(!match)return null;
 return {prefix:match[1],value:match[2],negative:match[2].startsWith('n')};
}
function order(rule,name,index){
 const item=info(rule,name);
 if(item.negative)return [10,index];
 const prefix=prefixes.indexOf(item.prefix);
 const rank=prefix<7?prefix:prefix<14?20+prefix-7:40+prefix-14;
 return [rank,item.value==='auto'?100:Number(item.value)];
}
function render(rule){
 return `${rule.selectors.map(selector=>selector.trim()).join(',\n')}\n{\n${rule.nodes.filter(node=>node.type==='decl').map(node=>`\t${node.prop}: ${node.value}${node.important?' !important':''};`).join('\n')}\n}\n`;
}
for(const name of ['base',...Object.keys(breakpoints)]){
 const container=name==='base'?root:root.nodes.find(node=>node.type==='atrule'&&node.params===`(min-width: ${breakpoints[name]}px)`);
 const original=container.nodes.filter(node=>node.type==='rule'&&info(node,name));
 const combined=[...original];
 let replayed=0;
 bootstrap.walkRules(rule=>{
  const media=rule.parent.type==='root'?'base':rule.parent.name==='media'?rule.parent.params:'';
  const matches=name==='base'?media==='base':media===`(min-width: ${breakpoints[name]}px)`;
  const item=info(rule,name);
  if(!matches||!item||item.negative||(!['auto','0','1','2','3','4','5'].includes(item.value)))return;
  if(combined.some(existing=>existing.selector.trim()===rule.selector.trim()))return;
  combined.push(rule);replayed++;
 });
 assert.ok(replayed>=100,`${name}: missing Bootstrap spacing`);
 const sorted=combined.map((rule,index)=>({rule,key:order(rule,name,index)})).sort((a,b)=>a.key[0]-b.key[0]||a.key[1]-b.key[1]);
 const end=container.nodes.find(node=>node.type==='comment'&&node.text===`END ${name.toUpperCase()} / SPACING`);
 assert.ok(end,name);original.forEach(rule=>rule.remove());
 const comment=postcss.comment({text:'Native spacing is replayed here to preserve breakpoint and shorthand/axis precedence after Bootstrap.'});
 comment.raws.before=`\n${name==='base'?'':'\t'}`;container.insertBefore(end,comment);
 for(const {rule} of sorted){
  const rendered=render(rule).split('\n').map(line=>name==='base'||!line?line:`\t${line}`).join('\n');
  const parsed=postcss.parse(rendered).nodes[0];parsed.raws.before=`\n\n${name==='base'?'':'\t'}`;
  container.insertBefore(end,parsed);
 }
 log.push({group:name,replayedNativeRules:replayed,totalSpacingRules:sorted.length});
}
const header=root.nodes.find(node=>node.type==='comment'&&node.text.includes('PHOENIX BOOTSTRAP EXTENSIONS'));
header.text=header.text.replace('Native Bootstrap utilities are not repeated here.','Native spacing is replayed per breakpoint for ordered overrides; other native utilities stay in Bootstrap.');
await fs.writeFile(target,root.toString());
await fs.writeFile('project-artifacts/css-extension-completion-20261106/spacing-compat.json',JSON.stringify(log,null,2));
console.log(`Replayed ${log.reduce((sum,item)=>sum+item.replayedNativeRules,0)} native spacing rules in ordered groups across ${log.length} breakpoint sections.`);
