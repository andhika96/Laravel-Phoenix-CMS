<?php

require __DIR__.'/bootstrap.php';
$catalog = app(App\Support\Article\ArticleTemplateCatalog::class);
$normalizer = app(App\Support\Article\ArticleTemplateOptions::class);
$archives = $catalog->archive();
$details = $catalog->detail();
$payload = [
    'settings' => ['archive_template' => 'minimal-reading-list', 'detail_template' => 'focused-reader', 'archive_per_page' => 6, 'archive_template_options' => $normalizer->archives(), 'detail_template_options' => $normalizer->details()],
    'templates' => ['archive' => $archives, 'detail' => $details],
    'copy' => ['previewTitle' => 'Article template preview', 'desktop' => 'Desktop', 'tablet' => 'Tablet', 'mobile' => 'Mobile', 'scaledToFit' => 'Scaled to fit', 'loadingPreview' => 'Loading preview', 'search' => 'Search', 'categoryFilter' => 'Category filter'],
];
$source = file_get_contents($root.'/resources/views/manage_article/templates/index.blade.php');
if (!preg_match('/<Teleport to="body">.*?<\/Teleport>/s', $source, $match)) throw new RuntimeException('Modal not found');
$markup = str_replace('rows="10"', ':rows="prototypeRevamp ? 3 : 10"', $match[0]);
$choices = '';
foreach (['archive' => $archives, 'detail' => $details] as $surface => $templates) {
    $choices .= '<optgroup label="'.ucfirst($surface).' templates">';
    foreach ($templates as $key => $template) $choices .= '<option value="'.$surface.':'.$key.'">'.htmlspecialchars($template['label']).'</option>';
    $choices .= '</optgroup>';
}
$rail = '<div class="prototype-rail"><label>Template<select id="prototype-template" @change="prototypeTemplate($event.target.value)">'.$choices.'</select></label><div class="prototype-comparison"><button type="button" :aria-pressed="prototypeRevamp" @click="prototypeAppearance(true)">Revamp</button><button type="button" :aria-pressed="!prototypeRevamp" @click="prototypeAppearance(false)">Saat ini</button></div><label>Notice<select id="prototype-notice" @change="prototypeNotify($event.target.value)"><option value="">Pilih contoh</option><option value="success">Success</option><option value="warning">Warning</option><option value="failed">Failed</option></select></label></div>';
$markup = str_replace('<div class="modal-content">', '<div class="modal-content">'.$rail, $markup);
$add = file_get_contents($root.'/resources/views/manage_article/manage_article_add.blade.php');
$noticeStart = strpos($add, '<div class="ph-notice" v-cloak>');
$noticeEnd = strpos($add, '<div class="row g-3">', $noticeStart);
if ($noticeStart === false || $noticeEnd === false) throw new RuntimeException('CMS notice not found');
$notice = substr($add, $noticeStart, $noticeEnd - $noticeStart);
$markup = str_replace('</Teleport>', $notice.'</Teleport>', $markup);
$markup = str_replace('class="article-template-options-dismiss"', 'class="article-template-options-dismiss ph-callout-warning ph-callout-no-border"', $markup);
file_put_contents(__DIR__.'/qa/modal.blade.php', $markup);
$body = view()->file(__DIR__.'/qa/modal.blade.php')->render();
$encoded = htmlspecialchars(json_encode($payload), ENT_QUOTES, 'UTF-8');
$html = '<!doctype html><html lang="id" data-bs-theme="light" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#1aa579"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Template Options · Preview revamp</title><link rel="icon" href="data:,"><link rel="stylesheet" href="assets/bootstrap.min.css"><link rel="stylesheet" href="assets/theme.css"><link rel="stylesheet" href="assets/phoenix-cms.css"><link rel="stylesheet" href="assets/noto_sans/fonts.css"><link rel="stylesheet" href="assets/fontawesome/css/all.min.css"><link rel="stylesheet" href="assets/coloris.css"><link rel="stylesheet" href="assets/buttons.css"><link rel="stylesheet" href="assets/manager.css"><link rel="stylesheet" id="revamp-css" href="revamp.css"><link rel="stylesheet" href="prototype.css"></head><body><div id="ph-app-manage-article-templates" data-preview-base-url="/preview/__SURFACE__/__TEMPLATE__" data-save-url="/save-demo" data-placeholder-thumbnail="/assets/images/alpine-lake.png" data-payload="'.$encoded.'"><div class="prototype-start"><p>Preview revamp Template Options</p><button id="article-template-options-trigger" class="btn ph-btn-theme" @click="openTemplateOptions">Buka Template Options</button></div>'.$body.'</div><script src="assets/vue.js"></script><script src="assets/bootstrap.js"></script><script src="assets/coloris.js"></script><script src="prototype.js"></script><script src="assets/manager.js"></script></body></html>';
file_put_contents(__DIR__.'/index.html', $html);
file_put_contents(__DIR__.'/qa/payload.json', json_encode($payload, JSON_PRETTY_PRINT));
echo "Rendered all eight templates using active modal controls and the Add Article CMS notice.\n";
