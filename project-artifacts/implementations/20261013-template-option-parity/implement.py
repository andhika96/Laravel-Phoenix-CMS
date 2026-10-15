from pathlib import Path
import shutil, datetime, re
root=Path.cwd()
backup=root/'project-artifacts/backups'/ (datetime.datetime.now().strftime('%Y%m%d_%H%M%S')+'-template-option-parity')
def edit(file, fn):
 p=root/file; old=p.read_text(encoding='utf-8'); new=fn(old)
 if old==new: return
 b=backup/file; b.parent.mkdir(parents=True,exist_ok=True); shutil.copy2(p,b)
 p.write_text(new,encoding='utf-8')
def normal(s):
 s=s.replace("$definition = $this->catalog->archive()", "if ($template === 'minimal-reading-list') {\n            $defaults = ['pagination.type' => 'soft', 'pagination.item_radius' => '0px', 'pagination.item_border_width' => '0px', 'pagination.item_border_color' => 'transparent', 'pagination.frame.border_width' => '0px', 'pagination.frame.border_color' => 'transparent', 'pagination.frame.radius' => '0px'];\n            foreach ($defaults as $path => $value) { if (!\\Illuminate\\Support\\Arr::has($input, $path)) data_set($input, $path, $value); }\n        }\n        $definition = $this->catalog->archive()",1)
 s=s.replace("'header' => [", "'display' => $this->displayOptions($input),\n            'header' => [")
 s=s.replace("'item_radius' =>", "'item_border_width' => $this->dimension(data_get($input, 'pagination.item_border_width'), '1px', self::BORDER_UNITS),\n            'item_radius' =>",1)
 anchor='    private function storyLayout('
 method="    private function displayOptions(array $input): array\n    {\n        $result = [];\n        foreach (['category', 'date', 'author', 'reading_time', 'excerpt', 'read_more', 'navigation'] as $field) {\n            $result[$field] = $this->boolean(data_get($input, 'display.'.$field), true);\n        }\n        return $result;\n    }\n\n"
 return s.replace(anchor,method+anchor)
edit('app/Support/Article/ArticleTemplateOptions.php',normal)
js='public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js'
def manager(s):
 s=s.replace("const options = value && typeof value === 'object' ? value : {};", "const options = value && typeof value === 'object' ? value : {};\n            options.display = options.display || {};\n            for (const field of ['category', 'date', 'author', 'reading_time', 'excerpt', 'read_more', 'navigation']) if (typeof options.display[field] !== 'boolean') options.display[field] = true;")
 s=s.replace("const sections = [{ key: 'header', label: 'Header content', icon: 'fa-align-left' }];", "const sections = [{ key: 'header', label: 'Header content', icon: 'fa-align-left' }];\n            if (!isArchive || ['minimal-reading-list', 'editorial-journal', 'mosaic-magazine'].includes(this.activeTemplateKey)) sections.push({ key: 'display', label: 'Visible elements', icon: 'fa-eye' });")
 return s
edit(js,manager)
def panel(s):
 anchor='<section v-if="optionsModal.section === \'header\'"'
 markup='''<section v-if="optionsModal.section === 'display'" id="article-template-option-panel-display" role="tabpanel" aria-labelledby="article-template-option-tab-display" class="article-template-options-section">
 <div class="article-template-options-section__heading"><div><h5>{{ t('Visible elements') }}</h5><small>{{ t('Choose which article details appear in this template.') }}</small></div></div>
 <div class="article-template-options-fields">
 @foreach (['category' => 'Show category', 'date' => 'Show date', 'author' => 'Show author / editor', 'reading_time' => 'Show reading time', 'excerpt' => 'Show excerpt', 'read_more' => 'Show Read more link', 'navigation' => 'Show previous and next articles'] as $field => $label)
 <label v-if="optionsModal.surface === 'detail' ? {{ in_array($field, ['date', 'author', 'navigation']) ? 'true' : 'false' }} : {{ in_array($field, ['category','date','excerpt']) ? 'true' : ($field === 'reading_time' ? \"optionsModal.key === 'minimal-reading-list'\" : ($field === 'read_more' ? \"optionsModal.key !== 'mosaic-magazine'\" : 'false')) }}" class="form-check form-switch"><input type="checkbox" class="form-check-input" v-model="optionsModal.value.display.{{ $field }}"><span class="form-check-label">{{ t($label) }}</span></label>
 @endforeach
 </div></section>
 '''
 return s.replace(anchor,markup+anchor,1)
edit('resources/views/manage_article/templates/index.blade.php',panel)
for name in ['minimal-reading-list','editorial-journal','mosaic-magazine']:
 def archive(s):
  s=re.sub(r'(<span class="article-chip">.*?</span>)',r"@if (data_get($templateOptions, 'display.category', true))\1@endif",s)
  s=re.sub(r'(<p class="article-excerpt-clamp">.*?</p>)',r"@if (data_get($templateOptions, 'display.excerpt', true))\1@endif",s)
  s=re.sub(r'(<small>\{\{ optional\(.*?</small>)',r"@if (data_get($templateOptions, 'display.date', true))\1@endif",s)
  s=re.sub(r'(<a class="article-text-link[^\n]*?</a>)',r"@if (data_get($templateOptions, 'display.read_more', true))\1@endif",s)
  if name=='minimal-reading-list':
   s=s.replace('<span>{{ $article->category?',"@if (data_get($templateOptions, 'display.category', true))<span>{{ $article->category?")
   s=s.replace("t('Uncategorized') }}</span><span>{{ optional($article->created_at)","t('Uncategorized') }}</span>@endif @if (data_get($templateOptions, 'display.date', true))<span>{{ optional($article->created_at)")
   s=s.replace("format('d M Y') }}</span><span>{{ $readingMinutes }}", "format('d M Y') }}</span>@endif @if (data_get($templateOptions, 'display.reading_time', true))<span>{{ $readingMinutes }}")
   s=s.replace("{{ t('min read') }}</span></div>","{{ t('min read') }}</span>@endif</div>")
  return s
 edit('resources/views/article/templates/archive/'+name+'.blade.php',archive)
edit('resources/views/article/templates/partials/detail-header-copy.blade.php',lambda s:s.replace('@if ($metaText)',"@php\n    $metaParts = [];\n    if (data_get($templateOptions, 'display.date', true) && $article->created_at) $metaParts[] = $article->created_at->format('d M Y');\n    $authorName = $article->author?->fullname ?: $article->author?->username;\n    if (data_get($templateOptions, 'display.author', true) && $authorName) $metaParts[] = t('By').' '.$authorName;\n    $metaText = implode(' · ', $metaParts);\n@endphp\n@if ($metaText)"))
edit('resources/views/article/templates/partials/detail-navigation.blade.php',lambda s:s.replace('@if (($previousArticle ?? null) || ($nextArticle ?? null))',"@if (data_get($templateOptions ?? [], 'display.navigation', true) && (($previousArticle ?? null) || ($nextArticle ?? null)))"))
edit('resources/views/manage_article/templates/partials/options-classic-balanced.blade.php',lambda s:s.replace("t('Story details')","t('Story details · metadata')").replace("t('Show author')","t('Show author / editor')"))
edit('resources/views/manage_article/templates/partials/options-styling.blade.php',lambda s:s.replace("<label>{{ t('Border color') }}<input class=\"form-control article-template-coloris\" type=\"text\" placeholder=\"Model default\" v-model=\"optionsModal.value.pagination.item_border_color\"></label>","<label>{{ t('Button border width') }}<span class=\"article-template-unit-control\"><input class=\"form-control\" type=\"number\" min=\"0\" :max=\"dimensionMax('pagination.item_border_width', 'border')\" :step=\"dimensionStep('pagination.item_border_width', 'border')\" :value=\"dimensionValue('pagination.item_border_width', 'border')\" @input=\"setDimensionValue('pagination.item_border_width', $event.target.value, 'border')\"><select class=\"form-select\" :value=\"dimensionUnit('pagination.item_border_width', 'border')\" @change=\"setDimensionUnit('pagination.item_border_width', $event.target.value, 'border')\"><option v-for=\"unit in unitChoices('border')\" :value=\"unit\">@{{ unit }}</option></select></span></label><label>{{ t('Border color') }}<input class=\"form-control article-template-coloris\" type=\"text\" placeholder=\"Model default\" v-model=\"optionsModal.value.pagination.item_border_color\"></label>"))
edit('resources/views/article/templates/partials/pagination.blade.php',lambda s:s.replace("$paginationStyle .= '--article-pagination-item-radius:","$paginationStyle .= '--article-pagination-item-border-width:'.data_get($paginationOptions, 'item_border_width', '1px').';';\n        $paginationStyle .= '--article-pagination-item-radius:"))
edit('public/assets/js/vue3/article/vueV3-article-frontend-2026.js',lambda s:s.replace("item_radius: safeDimension", "item_border_width: safeDimension(pagination.item_border_width, '1px'),\n            item_radius: safeDimension").replace("'--article-pagination-item-radius':", "'--article-pagination-item-border-width': pagination.item_border_width,\n            '--article-pagination-item-radius':"))
edit('public/assets/css/article/article-frontend-2026.css',lambda s:s+"\n/* Shared SSR and Vue pagination border setting. */\n.article-pagination .ph-pagination .page-link { border-width: var(--article-pagination-item-border-width, 1px); }\n.article-pagination.article-pagination--model-boxed .ph-pagination .page-link,\n.article-pagination.article-pagination--model-soft .ph-pagination .page-link { border-width: var(--article-pagination-item-border-width, 1px); }\n")
print(backup)
