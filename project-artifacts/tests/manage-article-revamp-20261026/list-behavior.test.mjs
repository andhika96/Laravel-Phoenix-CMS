import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';

const source=readFileSync('public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js','utf8');
function stateFor(axios={get:async()=>({data:{data:[],total:0,total_page:0}})}) {
    const document={querySelector:selector=>selector==='.ph-fetch-listdata-article'?{getAttribute:()=> 'https://phoenix.test/manage_article/listdata'}:null,getElementById:()=>null};
    const sandbox={createApp:options=>({mount:()=>options}),_: {debounce:fn=>fn},VuejsPaginateNext:{},URLSearchParams,axios,document,console,setTimeout,clearTimeout};
    vm.createContext(sandbox); vm.runInContext(source+';globalThis.options=ManageArticleVue3;',sandbox);
    const options=sandbox.options;
    const state={...options.data(),$nextTick:fn=>fn()};
    for(const [key,method] of Object.entries(options.methods))state[key]=method.bind(state);
    for(const [key,get] of Object.entries(options.computed))Object.defineProperty(state,key,{get:get.bind(state)});
    state.loadDataComplete=()=>{state.loading=false;state.loadingNextPage=false;};
    return state;
}
test('all list parameters survive pagination and status changes',async()=>{
    const calls=[];
    const state=stateFor({get:async(url,{params})=>{calls.push(Object.fromEntries(params));return {data:{data:[{id:7}],total:31,current_page:Number(params.get('page')),total_page:7}};}});
    Object.assign(state,{getQuerySearchUser:'R&D + mobile',getfilterByCategory:'12',getfilterByScheduled:'true',getfilterByStatus:'draft',articleSort:'title_desc',articlePageSize:5});
    await state.clickPaginate(3);
    assert.deepEqual(calls[0],{page:'3',per_page:'5',sort:'title_desc',search:'R&D + mobile',filter_by_status:'draft',filter_by_category:'12',filter_by_scheduled:'true'});
    await state.filterByStatus('pending');
    assert.equal(calls[1].page,'1');assert.equal(calls[1].search,'R&D + mobile');assert.equal(calls[1].filter_by_status,'pending');
});
test('out of order responses cannot replace a more recent query',async()=>{
    const pending=[];const state=stateFor({get:()=>new Promise(resolve=>pending.push(resolve))});
    const first=state.listData();state.getQuerySearchUser='latest';const second=state.refreshArticleList();
    pending[1]({data:{data:[{id:22}],total:1,current_page:1,total_page:1,status_counts:{all:1}}});await second;
    pending[0]({data:{data:[{id:11}],total:99,current_page:1,total_page:7}});await first;
    assert.equal(state.responseData[0].id,22);assert.equal(state.getTotalData,1);assert.equal(state.loadingNextPage,false);
});
test('network failure ends loading and provides retryable error state',async()=>{
    const state=stateFor({get:async()=>{throw new Error('offline');}});
    await state.listData();assert.ok(state.listError);assert.equal(state.loading,false);assert.equal(state.loadingNextPage,false);
});
test('detail toggling and selection are independent, and new queries clear selection',async()=>{
    const state=stateFor();state.responseData=[{id:1},{id:2}];
    state.clickSelectAll({target:{checked:true}});state.toggleExpandRow(1);
    assert.deepEqual([...state.selectedArticleIds],[1,2]);assert.equal(state.allArticlesSelected,true);assert.equal(state.responsiveExpandedRows[1],true);
    state.selectedArticleIds=[1];assert.equal(state.someArticlesSelected,true);
    await state.refreshArticleList();assert.deepEqual([...state.selectedArticleIds],[]);assert.equal(state.responsiveExpandedRows[1],undefined);
});
test('responsive layout always retains chevron, checkbox, title and actions',()=>{
    const state=stateFor();state.responsiveExpandedRows={17:true};
    for(const [width,hidden] of [[1200,[]],[900,[2]],[700,[2,5]],[320,[2,5,4]]]){
        state.getTableWidth=()=>width;state.recalcResponsive();assert.deepEqual([...state.responsiveHiddenCols],hidden);
        assert.equal(state.responsiveVisibleColCount,7-hidden.length);assert.equal(state.responsiveExpandedRows[17],true);
        for(const required of [7,0,1,6])assert.equal(state.isColHidden(required),false);
    }
});
