import assert from 'node:assert/strict';
import fs from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const source=fs.readFileSync('public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js','utf8');
function load(selection='') {
  const sandbox={_: {debounce:fn=>fn},VuejsPaginateNext:{},console,document:{},window:{getSelection:()=>({toString:()=>selection})},createApp:options=>({mount:()=>options}),requestAnimationFrame:fn=>fn(),setTimeout,clearTimeout};
  vm.createContext(sandbox);vm.runInContext(source+'\n;globalThis.options=ManageArticleVue3;',sandbox);
  return sandbox.options.methods;
}
const event=(blocked=false)=>({button:0,defaultPrevented:false,target:{closest:()=>blocked?{}:null}});

test('clicking row content toggles only the intended article',()=>{
  const methods=load(),state={responsiveExpandedRows:{7:true},toggleExpandRow(id){methods.toggleExpandRow.call(this,id);}};
  assert.equal(typeof methods.onArticleRowClick,'function');
  methods.onArticleRowClick.call(state,19,event());
  assert.equal(state.responsiveExpandedRows[19],true);assert.equal(state.responsiveExpandedRows[7],true);
  methods.onArticleRowClick.call(state,19,event());assert.equal(state.responsiveExpandedRows[19],false);
});
test('interactive descendants do not toggle the row',()=>{
  const methods=load(),state={toggleExpandRow(){assert.fail('Interactive controls must keep their own behavior');}};
  methods.onArticleRowClick.call(state,19,event(true));
});
test('prevented and secondary-button clicks do not toggle the row',()=>{
  const methods=load(),state={toggleExpandRow(){assert.fail('Ignored click toggled a row');}};
  methods.onArticleRowClick.call(state,19,{...event(),defaultPrevented:true});
  methods.onArticleRowClick.call(state,19,{...event(),button:1});
});
test('selecting text for copying does not toggle details',()=>{
  const methods=load('selected article text'),state={toggleExpandRow(){assert.fail('Text selection toggled a row');}};
  methods.onArticleRowClick.call(state,19,event());
});
