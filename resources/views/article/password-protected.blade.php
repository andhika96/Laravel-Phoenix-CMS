@extends('themes.'.custom_theme('frontend'))

@php($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config()))

@section('title'){{ t('Protected article') }}@endsection

@push('meta')<meta name="robots" content="noindex,nofollow">@endpush

@section('content')
    <main id="ph-article-password-gate" class="article-access-page article-access-page--password" data-unlock-url="{{ $unlockUrl }}" data-redirect-url="{{ $redirectUrl }}" data-initial-error="{{ $errors->first('password') }}">
        <section class="article-access-state" aria-labelledby="article-password-title">
            <a href="{{ route('cms.core.article') }}" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> {{ t('Back to articles') }}</a>
            <div class="article-access-state__veil" aria-hidden="true"></div>
            <div class="article-password-modal" role="dialog" aria-modal="true" aria-labelledby="article-password-title" aria-describedby="article-password-description">
                <a href="{{ route('cms.core.article') }}" class="article-password-modal__close" aria-label="{{ t('Close') }}"><i class="fas fa-times" aria-hidden="true"></i></a>
                <span class="article-password-modal__icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
                <h1 id="article-password-title">{{ t('Protected article') }}</h1>
                <p id="article-password-description">{{ t('Enter the password set by the author to continue reading.') }}</p>
                <form action="{{ $unlockUrl }}" method="post" v-on:submit.prevent="unlock">
                    @csrf
                    <label for="article-password-input">{{ t('Password') }}</label>
                    <div class="article-password-modal__input-wrap">
                        <input id="article-password-input" name="password" v-bind:type="showPassword ? 'text' : 'password'" v-model="password" autocomplete="current-password" required maxlength="128" v-bind:aria-invalid="error ? 'true' : 'false'" aria-describedby="article-password-error">
                        <button type="button" class="article-password-modal__toggle" v-on:click="showPassword = !showPassword" v-bind:aria-label="showPassword ? 'Hide password' : 'Show password'"><i v-bind:class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" aria-hidden="true"></i></button>
                    </div>
                    <p id="article-password-error" class="article-password-modal__error" role="alert" v-show="error" v-text="error">@error('password'){{ $message }}@enderror</p>
                    <div class="article-password-modal__actions">
                        <a href="{{ route('cms.core.article') }}" class="btn ph-btn-theme-outline">{{ t('Back to articles') }}</a>
                        <button type="submit" class="btn ph-btn-theme" v-bind:disabled="isSubmitting"><span v-if="!isSubmitting">{{ t('Unlock article') }}</span><span v-else>{{ t('Unlocking') }}…</span></button>
                    </div>
                </form>
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

@push('js')
    <script src="{{ url('assets/js/article/article-theme-color-sync-2026.js?v=').time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.5.21/dist/vue.global.prod.js" crossorigin="anonymous"></script>
    <script src="{{ url('assets/js/vue3/article/vueV3-article-frontend-2026.js?v=').time() }}"></script>
@endpush
