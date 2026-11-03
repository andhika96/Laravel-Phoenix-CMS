import fs from 'node:fs';
import assert from 'node:assert/strict';
import postcss from 'postcss';
const pages=['login','signup','forgotpassword','resetpassword'];
for(const template of ['default','card','split_left','split_right']) for(const page of pages){
 const source=fs.readFileSync(`resources/views/auth/${template}/${page}.blade.php`,'utf8');
 assert(source.includes("@extends('auth.auth_layout')"));
 assert(!source.includes('<style>'));
 assert(source.includes(`@section('auth-template', '${template}')`));
}
for(const page of ['login','signup','forgot-password','reset-password']){
 const css=postcss.parse(fs.readFileSync(`public/assets/css/auth/${page}.css`,'utf8'));
 css.walkRules(rule=>rule.selectors.forEach(selector=>assert(selector.startsWith('body.ph-auth-page[data-auth-layout='))));
}
const layout=fs.readFileSync('resources/views/auth/auth_layout.blade.php','utf8');
assert(!layout.includes('assets/css/themes/'));
assert(!layout.includes('shared/phoenix-cms'));
assert(layout.includes('auth-base.css'));
console.log('PASS: 16 independent views, four scoped styles, independent CSS links.');
