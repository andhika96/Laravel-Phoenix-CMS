@php
    $templateOptions = app(\App\Support\Article\ArticleTemplateOptions::class)->detail('focused-reader', $templateOptions ?? []);
    $reader = $templateOptions['focused_reader'];
    $readerStyle = '';
    foreach (\App\Support\Article\ArticleTemplateOptions::FOCUSED_READER_FIELDS as $field => $spec) {
        foreach (['desktop', 'tablet', 'mobile'] as $device) {
            $readerStyle .= '--reader-'.str_replace('_', '-', $field).'-'.$device.':'.$reader[$field][$device].$spec[6].';';
        }
    }
    foreach (['reading_width', 'cover_width', 'title_width'] as $field) $readerStyle .= '--reader-'.str_replace('_', '-', $field).':'.$reader[$field].'px;';
    foreach (['desktop', 'tablet', 'mobile'] as $device) {
        $readerStyle .= '--reader-align-'.$device.':'.$reader['alignment'][$device].';';
        $readerStyle .= '--reader-justify-'.$device.':'.($reader['alignment'][$device] === 'left' ? 'flex-start' : 'center').';';
        $readerStyle .= '--reader-ratio-'.$device.':'.$reader['cover_ratio'][$device].';';
    }
    $readerStyle .= '--reader-cover-fit:'.$reader['cover_fit'].';--reader-cover-position:'.$reader['cover_position'].';';
    $coverUrl = $article->thumb_l && Storage::disk('public')->exists($article->thumb_l) ? Storage::url($article->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
    $eyebrow = $templateOptions['header']['eyebrow'];
    $description = $templateOptions['header']['description'];
    $eyebrowText = $eyebrow['mode'] === 'custom' && $eyebrow['text'] !== '' ? $eyebrow['text'] : ($article->category?->name ?? t('Uncategorized'));
    $descriptionText = $description['mode'] === 'custom' && $description['text'] !== '' ? $description['text'] : \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180);
    $authorName = $article->author?->fullname ?: $article->author?->username;
    $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 220));
    $hasMeta = ($templateOptions['display']['author'] && $authorName) || ($templateOptions['display']['date'] && $article->created_at) || $templateOptions['display']['reading_time'];
@endphp
<article class="article-detail article-detail--focused-reader" style="{{ $readerStyle }}" data-reader-cover-width="{{ $reader['cover_width_mode'] }}" data-reader-thumbnails="{{ $reader['navigation_thumbnails'] ? 'true' : 'false' }}" data-reader-divider="{{ $reader['navigation_divider'] ? 'true' : 'false' }}" data-reader-quote-accent="{{ $reader['quote_accent'] ? 'true' : 'false' }}">
    <div class="article-detail__shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        <a href="{{ route('cms.core.article') }}" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> {{ t('Back to articles') }}</a>
        <header class="article-detail__header" data-reader-meta-position="{{ $reader['metadata_position'] }}">
            @include('article.templates.partials.detail-header-copy', ['focusedReaderHeader' => true])
        </header>
        @if ($reader['cover_enabled'])
            <figure class="article-reader-cover">
                <img src="{{ $coverUrl }}" alt="{{ $article->title }}" class="article-detail__cover article-media-frame" fetchpriority="high">
                @if ($reader['cover_caption'] !== '')<figcaption>{{ $reader['cover_caption'] }}</figcaption>@endif
            </figure>
        @endif
        <div class="article-rich-content">{!! $article->content !!}</div>
        @include('article.templates.partials.detail-navigation', ['previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null, 'readingCards' => true])
    </div>
</article>
