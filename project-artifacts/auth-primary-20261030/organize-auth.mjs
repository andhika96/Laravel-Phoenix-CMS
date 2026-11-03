import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import postcss from 'postcss';
if (Date.now() >= Date.parse('2026-10-30T09:50:00Z')) throw Error('Deadline reached');
const root = process.cwd();
const manifest = JSON.parse(fs.readFileSync('project-artifacts/auth-primary-20261030/backup-manifest.json'));
const pages = {login: ['login','login'], signup: ['signup','signup'], forgotpassword: ['forgotPassword','forgot-password'], resetpassword: ['resetPassword','reset-password']};
const cssDirectory = path.join(root,'public/assets/css/auth');
fs.mkdirSync(cssDirectory,{recursive:true});
for (const [file,[page,cssName]] of Object.entries(pages)) {
 const styles=[];
 for (const template of ['default','card','split_left','split_right']) {
  const relative=`resources/views/auth/${template}/${file}.blade.php`;
  const source=fs.readFileSync(relative,'utf8');
  const original=manifest.files.find(item=>item.file===relative);
  if(crypto.createHash('sha256').update(source).digest('hex')!==original.sha256) throw Error(`Source changed: ${relative}`);
  const match=source.match(/\s*<style>([\s\S]*?)<\/style>/);
  if(!match) throw Error(`Style missing: ${relative}`);
  const css=postcss.parse(match[1]);
  css.walkRules(rule=> {rule.selectors=rule.selectors.map(selector=>`body.ph-auth-page[data-auth-layout="${template}"] ${selector.trim()}`);});
  styles.push(`/* ${template} */\n${css.toString().trim()}`);
  const result=source.replace("@extends('themes.'.custom_theme('auth'))",`@extends('auth.auth_layout')\r\n\r\n@section('auth-page', '${page}')\r\n@section('auth-template', '${template}')`).replace(match[0],'');
  fs.writeFileSync(relative,result);
 }
 fs.writeFileSync(path.join(cssDirectory,`${cssName}.css`),styles.join('\n\n')+'\n');
}
let layout=fs.readFileSync('resources/views/themes/arunika_lucent/auth/auth_layout.blade.php','utf8');
layout=layout.replace('$siteTypography =',`$authPage = trim($__env->yieldContent('auth-page', 'login'));\n\t$authStyles = ['login' => 'login', 'signup' => 'signup', 'forgotPassword' => 'forgot-password', 'resetPassword' => 'reset-password'];\n\t$authPage = array_key_exists($authPage, $authStyles) ? $authPage : 'login';\n\t$authSettings = custom_page_theme_setting($authPage);\n\t$authPalette = \\App\\Support\\AuthAppearance::palette($authSettings['page_primary_color'] ?? null);\n\t$siteTypography =`);
layout=layout.replace(/\s*<link href="\{\{ asset\('assets\/css\/(?:shared\/phoenix-cms.css|themes\/arunika_lucent\/arunika_lucent.css|shared\/theme-responsive-typography.css)[^\n]+/g,'');
layout=layout.replace("@stack('css')",`<link href="{{ asset('assets/css/auth/auth-base.css?v=').filemtime(public_path('assets/css/auth/auth-base.css')) }}" rel="stylesheet">\n\t\t\t<link href="{{ asset('assets/css/auth/'.$authStyles[$authPage].'.css?v=').filemtime(public_path('assets/css/auth/'.$authStyles[$authPage].'.css')) }}" rel="stylesheet">\n\n\t\t\t@stack('css')`);
const start=layout.indexOf('\t\t\t<style>');
const end=layout.indexOf('</style>',start)+8;
layout=layout.slice(0,start)+`\t\t\t<style>\n\t\t\t\tbody.ph-auth-page\n\t\t\t\t{\n\t\t\t\t\t--page-header-background: {{ $authSettings['page_color_nuances'] ?? '#1FA6759F' }};\n\t\t\t\t\t--auth-primary: {{ $authPalette['primary'] }};\n\t\t\t\t\t--auth-primary-hover: {{ $authPalette['hover'] }};\n\t\t\t\t\t--auth-primary-active: {{ $authPalette['active'] }};\n\t\t\t\t\t--auth-primary-disabled: {{ $authPalette['disabled'] }};\n\t\t\t\t\t--auth-primary-text: {{ $authPalette['text'] }};\n\t\t\t\t\t--auth-primary-hover-text: {{ $authPalette['hover_text'] }};\n\t\t\t\t\t--auth-primary-active-text: {{ $authPalette['active_text'] }};\n\t\t\t\t\t--auth-primary-rgb: {{ $authPalette['rgb'] }};\n\t\t\t\t}\n\t\t\t</style>`+layout.slice(end);
layout=layout.replace('<body class="ph-theme-arunika-lucent">','<body class="ph-auth-page" data-auth-page="{{ $authPage }}" data-auth-layout="@yield(\'auth-template\')">');
fs.writeFileSync('resources/views/auth/auth_layout.blade.php',layout);
console.log('Moved 16 template style blocks into four scoped auth styles; created independent auth layout.');
