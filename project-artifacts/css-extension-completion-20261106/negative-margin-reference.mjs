import fs from 'node:fs/promises';
import * as sass from 'sass';
import {chromium} from 'playwright-core';
import assert from 'node:assert/strict';
const source=`@import "bootstrap/scss/functions";
$enable-negative-margins: true;
$spacers: (0:0,1:.25rem,2:.5rem,3:1rem,4:1.5rem,5:3rem,6:3.5rem);
@import "bootstrap/scss/variables";
@import "bootstrap/scss/variables-dark";
@import "bootstrap/scss/maps";
@import "bootstrap/scss/mixins";
@import "bootstrap/scss/utilities";
@import "bootstrap/scss/utilities/api";`;
const reference=sass.compileString(source,{loadPaths:['node_modules'],logger:{warn(){},debug(){}}}).css;
const extension=await fs.readFile('public/assets/css/shared/extending-css-bootstrap-5.css','utf8');
const bootstrap=await fs.readFile('public/assets/plugins/bootstrap/5.3.6_custom/bootstrap.css','utf8');
const browser=await chromium.launch({channel:'chrome',headless:true}),results=[];
try{
 for(const [name,css] of [['Bootstrap Utilities API negative enabled',reference],['Phoenix extension',bootstrap+extension]]){
  const page=await browser.newPage({viewport:{width:1200,height:800}});await page.setContent('<div id="target"></div>');await page.addStyleTag({content:'html{font-size:16px}'+css});
  for(const [classes,property,expected] of [['m-n3 mt-1','margin-top','-16px'],['m-n3 mt-6','margin-top','-16px'],['mx-n3 ms-6','margin-left','-16px'],['m-n3 mt-md-6','margin-top','56px']]){
   const actual=await page.locator('#target').evaluate((el,item)=>{el.className=item.classes;return getComputedStyle(el).getPropertyValue(item.property);},{classes,property});assert.equal(actual,expected);results.push({name,classes,property,actual});
  }await page.close();
 }
 await fs.writeFile('project-artifacts/css-extension-completion-20261106/negative-margin-reference.json',JSON.stringify(results,null,2));
 console.log('8 reference checks passed: negative margin group precedence matches Bootstrap Utilities API and legacy behavior.');
}finally{await browser.close();}
