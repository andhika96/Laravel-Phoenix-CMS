import fs from 'node:fs';
import postcss from 'postcss';
import assert from 'node:assert/strict';
const themes=['arunika_prism','arunika_aurora','arunika_lucent','arunika_equinox','default','calm_green'];
for(const theme of themes){
 const layout=fs.readFileSync(`resources/views/themes/${theme}/cms/cms_layout.blade.php`,'utf8');
 assert(!layout.includes('assets/css/auth/'));
 assert(!layout.includes('AuthAppearance'));
 assert(!layout.includes('--auth-primary'));
}
for(const file of fs.readdirSync('public/assets/css/auth')){
 const css=postcss.parse(fs.readFileSync(`public/assets/css/auth/${file}`,'utf8'));
 css.walkRules(rule=>{if(rule.parent.type==='atrule'&&rule.parent.name.includes('keyframes'))return;rule.selectors.forEach(selector=>assert(selector.startsWith('.ph-auth-page ')||selector.startsWith('body.ph-auth-page'),selector));});
}
for(const template of ['default','card','split_left','split_right'])for(const page of ['login','signup','forgotpassword','resetpassword']){
 const blade=fs.readFileSync(`resources/views/auth/${template}/${page}.blade.php`,'utf8');
 assert(blade.includes("@extends('auth.auth_layout')"));
 assert(!blade.includes('custom_theme('));
}
const auth=fs.readFileSync('resources/views/auth/auth_layout.blade.php','utf8');
assert(!auth.includes('assets/css/themes/'));
assert(!auth.includes('cms-bootstrap-theme'));
assert(!auth.includes('shared/phoenix-cms'));
console.log('PASS: six CMS layouts exclude auth assets; all auth CSS scoped; all 16 auth views independent.');
