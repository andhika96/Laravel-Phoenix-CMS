@extends('themes.'.custom_theme('frontend'))

@php($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config()))

@section('title')
    {{ $article->title }}
@endsection

@section('content')
    <main id="ph-article-detail">
        @include($detailView, ['article' => $article, 'templateSettings' => $templateSettings, 'templateOptions' => $templateOptions, 'previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null])
    </main>
@endsection

@push('css')
    <link href="{{ asset('storage/fonts/'.$articleTypography['fontFamilyCode'].'/fonts.css?v=').time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme-responsive-typography.css?v=').time() }}" rel="stylesheet">
    <style>:root{--ph-font-family:'{{ $articleTypography['fontFamilyName'] }}',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;--ph-font-size:{{ $articleTypography['fontSize'] }};}</style>
    <link href="{{ url('assets/css/article/article-frontend-2026.css?v=').time() }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ url('assets/js/article/article-theme-color-sync-2026.js?v=').time() }}"></script>
@endpush
