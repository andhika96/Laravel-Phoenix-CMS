@extends('themes.'.custom_theme('frontend'))

@php($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config()))

@section('title')
    {{ t('Articles') }}
@endsection

@section('content')
    <main id="ph-app-article-frontend" data-list-url="{{ route('cms.core.article.listdata') }}">
        @include($archiveView, ['articles' => $articles, 'templateSettings' => $templateSettings])
        <span id="ph-article-vue-bridge" aria-hidden="true"></span>
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
    <script src="https://cdn.jsdelivr.net/npm/vue@3.5.21/dist/vue.global.prod.js" crossorigin="anonymous"></script>
    <script src="{{ url('assets/js/vue3/article/vueV3-article-frontend-2026.js?v=').time() }}"></script>
@endpush
