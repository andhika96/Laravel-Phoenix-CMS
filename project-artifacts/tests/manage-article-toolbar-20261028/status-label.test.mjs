import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const source=readFileSync('public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js','utf8');
const sandbox={createApp:options=>({mount:()=>options}),_: {debounce:fn=>fn},VuejsPaginateNext:{},console,setTimeout,clearTimeout};
vm.createContext(sandbox);
vm.runInContext(source+';globalThis.options=ManageArticleVue3;',sandbox);
test('status trigger label follows the server-provided statuses and falls back to All',()=>{
    const label=sandbox.options.computed.articleStatusLabel;
    assert.equal(typeof label,'function');
    const state={getfilterByStatus:'',articleStatuses:[{code_name:'publish',name:'Publish'},{code_name:'pending',name:'Menunggu Review'}]};
    assert.equal(label.call(state),'All');
    state.getfilterByStatus='publish';assert.equal(label.call(state),'Publish');
    state.getfilterByStatus='pending';assert.equal(label.call(state),'Menunggu Review');
    state.getfilterByStatus='missing';assert.equal(label.call(state),'All');
    state.articleStatuses=[];assert.equal(label.call(state),'All');
});
