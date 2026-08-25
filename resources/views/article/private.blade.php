@extends('themes.'.custom_theme('frontend'))

@php($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config()))

@section('title'){{ t('Private article') }}@endsection

@push('meta')<meta name="robots" content="noindex,nofollow">@endpush

@section('content')
    <main class="article-access-page article-access-page--private">
        <section class="article-access-state" aria-labelledby="article-private-title">
            <a href="{{ route('cms.core.article') }}" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> {{ t('Back to articles') }}</a>
            <div class="article-access-state__content">
                <span class="article-access-state__icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
                <h1 id="article-private-title">{{ t('This article is private') }}</h1>
                <p>{{ t('This content is only available to its author and permitted readers.') }}</p>
                <a href="{{ route('cms.core.article') }}" class="article-text-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> {{ t('Back to articles') }}</a>
            </div>
        </section>
    </main>
@endsection

@push('css')
    <link href="{{ asset('storage/fonts/'.$articleTypography['fontFamilyCode'].'/fonts.css?v=').time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme-responsive-typography.css?v=').time() }}" rel="stylesheet">
    <style>:root{--ph-font-family:'{{ $articleTypography['fontFamilyName'] }}',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;--ph-font-size:{{ $articleTypography['fontSize'] }};}</style>
    <link href="{{ url('assets/css/article/article-frontend-2026.css?v=').time() }}" rel="stylesheet">
@endpush

@push('js')<script src="{{ url('assets/js/article/article-theme-color-sync-2026.js?v=').time() }}"></script>@endpush
