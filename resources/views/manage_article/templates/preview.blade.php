@php($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config()))
<!doctype html>
<html lang="en" style="--ph-font-family:'{{ $articleTypography['fontFamilyName'] }}',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;--ph-font-size:{{ $articleTypography['fontSize'] }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}?v={{ @filemtime(public_path('assets/plugins/fontawesome/5.15.3/css/all.min.css')) }}" rel="stylesheet">
    <link href="{{ asset('storage/fonts/'.$articleTypography['fontFamilyCode'].'/fonts.css?v=').time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme-responsive-typography.css?v=').time() }}" rel="stylesheet">
    <script src="{{ url('assets/js/article/article-theme-color-sync-2026.js?v=').time() }}"></script>
    <link href="{{ url('assets/css/article/article-frontend-2026.css?v=').time() }}" rel="stylesheet">
    <style>body{margin:0;background:#fff}.article-preview-fixture-note{position:fixed;top:12px;right:14px;z-index:10;padding:4px 8px;border:1px solid var(--article-accent-ring,#ded5ff);border-radius:999px;background:rgb(255 255 255 / .92);color:var(--article-accent,#6542d7);font:700 11px/1 var(--ph-font-family,Arial,sans-serif);letter-spacing:.04em;text-transform:uppercase}</style>
</head>
<body @if ($isPreviewFixture ?? false) data-preview-fixture="true" @endif>
    @if ($isPreviewFixture ?? false)<span class="article-preview-fixture-note">{{ t('Sample editorial content') }}</span>@endif
    @if ($surface === 'archive')
        @include($templateView, ['articles' => $articles, 'templateSettings' => $templateSettings, 'templateOptions' => $templateOptions, 'previousArticle' => $previousArticle, 'nextArticle' => $nextArticle, 'articleCategories' => $articleCategories ?? collect(), 'popularArticles' => $popularArticles ?? collect(), 'previewDevice' => $previewDevice ?? null])
    @elseif ($article)
        @include($templateView, ['article' => $article, 'templateSettings' => $templateSettings, 'templateOptions' => $templateOptions, 'previousArticle' => $previousArticle, 'nextArticle' => $nextArticle])
    @else
        <div class="article-empty">{{ t('Create a published public Article to preview a detail template.') }}</div>
    @endif
    @if ($isPreviewFixture ?? false)
        <script>document.addEventListener('click', function (event) { if (event.target.closest('a')) event.preventDefault(); });document.addEventListener('submit', function (event) { if (event.target?.closest?.('[data-article-filter]')) event.preventDefault(); });</script>
    @endif
</body>
</html>
