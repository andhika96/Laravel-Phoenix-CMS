<?php

$fixtureRoot = dirname(__DIR__, 2).'/mockups/20261012_template-options-ui-revamp';
require $fixtureRoot.'/bootstrap.php';
$payload = json_decode(file_get_contents($fixtureRoot.'/qa/payload.json'), true, flags: JSON_THROW_ON_ERROR);
$source = file_get_contents($root.'/resources/views/manage_article/templates/index.blade.php');
if (!preg_match('/<Teleport to="body">.*?<\/Teleport>/s', $source, $match)) throw new RuntimeException('Modal not found');
$choices = '';
foreach ($payload['templates'] as $surface => $templates) {
    foreach ($templates as $key => $template) $choices .= '<option value="'.$surface.':'.$key.'">'.htmlspecialchars($template['label']).'</option>';
}
$toolbar = '<div class="qc-toolbar"><span>Source runtime QC</span><select id="prototype-template">'.$choices.'</select><select id="prototype-notice"><option value="">Notice</option><option value="success">Success</option><option value="warning">Warning</option><option value="failed">Failed</option></select></div>';
$markup = str_replace('<div class="modal-content">', '<div class="modal-content">'.$toolbar, $match[0]);
file_put_contents(__DIR__.'/runtime-modal.blade.php', $markup);
$body = view()->file(__DIR__.'/runtime-modal.blade.php')->render();
$encoded = htmlspecialchars(json_encode($payload), ENT_QUOTES, 'UTF-8');
$head = '<!doctype html><html lang="id" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#1aa579"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" href="data:,"><title>Template Options — source runtime QC</title>';
foreach (['bootstrap.min.css', 'theme.css', 'phoenix-cms.css', 'noto_sans/fonts.css', 'fontawesome/css/all.min.css', 'coloris.css', 'buttons.css', 'manager.css'] as $stylesheet) $head .= '<link rel="stylesheet" href="/assets/'.$stylesheet.'">';
$head .= '<style>body{margin:0;font-size:14px;font-family:var(--ph-font-family)}.qc-toolbar{display:flex;flex-wrap:wrap;align-items:center;gap:12px;padding:8px 20px;background:#f6f8fa;font-size:12px;border-bottom:1px solid #e4e7ec}.qc-toolbar select{height:30px;max-width:250px;font-size:12px}</style></head><body>';
$html = $head.'<div id="ph-app-manage-article-templates" data-payload="'.$encoded.'" data-preview-base-url="/preview/__SURFACE__/__TEMPLATE__" data-save-url="/save-demo"><button id="article-template-options-trigger" @click="openTemplateOptions">Template Options</button>'.$body.'</div>';
foreach (['vue.js', 'bootstrap.js', 'coloris.js', 'manager.js'] as $script) $html .= '<script src="/assets/'.$script.'"></script>';
$html .= '<script>window.prototypeVm=window.ManageArticleTemplateVue3;const vm=window.prototypeVm;document.getElementById("prototype-template").addEventListener("change",e=>{const [surface,key]=e.target.value.split(":");vm.optionsModal.dirty=false;vm.modalPreviewUrl="";vm.surface=surface;vm.selectTemplate(key);vm.$nextTick(()=>vm.openTemplateOptions())});document.getElementById("prototype-notice").addEventListener("change",e=>vm.showNotice(e.target.value,e.target.value==="success"?"Changes applied to the draft. Save Template to persist them.":e.target.value==="warning"?"You have unsaved changes.":"Unable to load the preview."));vm.$nextTick(()=>vm.openTemplateOptions());</script></body></html>';
file_put_contents(__DIR__.'/index.html', $html);
echo "Active source modal compiled without the prototype stylesheet or method overrides.\n";
