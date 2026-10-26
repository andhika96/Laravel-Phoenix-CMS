test('usable table width excludes the browser scrollbar gutter', () =>
{
    const options = loadManageArticleOptions({getElementById:()=>({clientWidth:1753,offsetWidth:1768})});
    assert.equal(options.methods.getTableWidth(),1753);
});

test('narrow containers move metadata while keeping primary controls visible', () =>
{
    const options = loadManageArticleOptions({});
    for (const [width, expected] of [[1200,[]],[940,[2]],[740,[2,5]],[600,[2,5,4]],[320,[2,5,4]]]) {
        const state={getTableWidth:()=>width,responsiveHiddenCols:[],responsiveExpandedRows:{19:true}};
        options.methods.recalcResponsive.call(state);
        assert.deepEqual([...state.responsiveHiddenCols],expected);
        for (const column of [7,0,1,6]) assert.equal(state.responsiveHiddenCols.includes(column),false);
        assert.equal(state.responsiveExpandedRows[19],true);
        assert.equal(options.computed.responsiveVisibleColCount.call(state),7-expected.length);
    }
});

test('resizing back to desktop restores columns without collapsing open details', () =>
{
    const options = loadManageArticleOptions({});
    const state={getTableWidth:()=>1200,responsiveHiddenCols:[2,5,4],responsiveExpandedRows:{4:true}};
    options.methods.recalcResponsive.call(state);
    assert.deepEqual([...state.responsiveHiddenCols],[]);
    assert.equal(state.responsiveExpandedRows[4],true);
});

test('temporarily hidden wrappers do not change the current column state', () =>
{
    const options = loadManageArticleOptions({});
    const state={getTableWidth:()=>0,responsiveHiddenCols:[2,5,4]};
    options.methods.recalcResponsive.call(state);
    assert.deepEqual([...state.responsiveHiddenCols],[2,5,4]);
});
