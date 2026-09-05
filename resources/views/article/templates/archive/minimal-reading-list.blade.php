@php
    $categories = collect($articleCategories ?? [])->take(10);
    $popularArticles = collect($popularArticles ?? [])->take(4);
    $activeCategory = (string) request('category', '');
    $sidebarOptions = ($templateOptions ?? [])['sidebar'] ?? [];
    $categoryOptions = ($templateOptions ?? [])['toolbar']['category'] ?? [];
    $sidebarEnabled = data_get($sidebarOptions, 'enabled', true) !== false;
    $categoriesEnabled = $sidebarEnabled && data_get($sidebarOptions, 'categories.enabled', true) !== false;
    $popularEnabled = $sidebarEnabled && data_get($sidebarOptions, 'popular.enabled', true) !== false;
    $categoryFilterEnabled = data_get($categoryOptions, 'enabled', false) !== false;
    $categoryMode = data_get($categoryOptions, 'mode', 'button-list');
    $categoryMode = in_array($categoryMode, ['select', 'button-list'], true) ? $categoryMode : 'button-list';
    $categoriesPosition = data_get($sidebarOptions, 'categories.position', 'static');
    $categoriesPosition = in_array($categoriesPosition, ['static', 'sticky'], true) ? $categoriesPosition : 'static';
    $popularPosition = data_get($sidebarOptions, 'popular.position', 'static');
    $popularPosition = in_array($popularPosition, ['static', 'sticky'], true) ? $popularPosition : 'static';
    $postListGap = data_get(($templateOptions ?? [])['post_list'] ?? [], 'item_gap', '0.75rem');
    $postListGap = is_string($postListGap) && preg_match('/^\d+(?:\.\d+)?(?:px|em|rem|%|pt)$/', $postListGap) === 1 ? $postListGap : '0.75rem';
    $categoriesPanelVisible = $categoryFilterEnabled && $categoryMode === 'button-list' && $categoriesEnabled;
    $sidebarVisible = $categoriesPanelVisible || $popularEnabled;
@endphp

<section class="article-page article-page--reading-list">
    <div class="article-shell" @include('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []])>
        @include('article.templates.partials.archive-header', ['templateKey' => 'minimal-reading-list', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Minimal Reading List'), 'defaultDescription' => t('Thoughtful reads on design, technology, and the web.')])

        <div class="article-reading-list-layout {{ $sidebarVisible ? '' : 'article-reading-list-layout--without-sidebar' }}">
            <div class="article-reading-list__main">
                <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
                <div data-article-vue-list-content>
                    <div class="article-reading-list" data-article-list style="--article-reading-list-post-gap:{{ $postListGap }}">
                        @forelse ($articles as $article)
                            @php($readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 220)))
                            @php($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                            <article class="article-reading-list__item">
                                @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-reading-list__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title])
                                <div class="article-reading-list__body">
                                    <div class="article-meta"><span>{{ $article->category?->name ?? t('Uncategorized') }}</span><span>{{ optional($article->created_at)->format('d M Y') }}</span><span>{{ $readingMinutes }} {{ t('min read') }}</span></div>
                                    @include('article.templates.partials.archive-title')
                                    <p class="article-excerpt-clamp">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180) }}</p>
                                    <a class="article-text-link article-reading-list__read-link" href="{{ route('cms.core.article.detail', $article->uri) }}">{{ t('Read more') }} <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                                </div>
                            </article>
                        @empty
                            <p class="article-empty">{{ t('No articles found') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="article-vue-control-slot" data-article-vue-control-slot></div>
                @include('article.templates.partials.pagination', ['articles' => $articles])
            </div>

            @if ($sidebarVisible)
                <aside class="article-reading-list__sidebar" aria-label="{{ t('Article discovery') }}">
                    @if ($categoriesPanelVisible)
                        <section class="article-reading-list__sidebar-panel article-reading-list__categories" data-article-sidebar-position="{{ $categoriesPosition }}">
                            <h2>{{ t('Categories') }}</h2>
                            <form class="article-reading-list__category-search" action="{{ route('cms.core.article') }}" method="get" role="search" data-article-category-search-form>
                                <label class="visually-hidden" for="article-minimal-reading-list-category-search">{{ t('Search categories') }}</label>
                                <input id="article-minimal-reading-list-category-search" type="search" placeholder="{{ t('Search categories') }}" autocomplete="off" data-article-category-search>
                            </form>
                            <div class="article-reading-list__category-list">
                                @if ($categories->isNotEmpty())
                                    @php($allCategoryQuery = request()->except(['page', 'category']))
                                    <a class="article-reading-list__category-link {{ $activeCategory === '' ? 'is-active' : '' }}" href="{{ route('cms.core.article', $allCategoryQuery) }}" data-article-category-link data-category-id="" data-category-label="{{ \Illuminate\Support\Str::lower(t('All categories')) }}" @if ($activeCategory === '') aria-current="page" @endif>{{ t('All categories') }}</a>
                                    @foreach ($categories as $category)
                                        @php($categoryQuery = array_merge(request()->except(['page', 'category']), ['category' => $category->id]))
                                        <a class="article-reading-list__category-link {{ $activeCategory === (string) $category->id ? 'is-active' : '' }}" href="{{ route('cms.core.article', $categoryQuery) }}" data-article-category-link data-category-id="{{ $category->id }}" data-category-label="{{ \Illuminate\Support\Str::lower((string) $category->name) }}" @if ($activeCategory === (string) $category->id) aria-current="page" @endif>{{ $category->name }}</a>
                                    @endforeach
                                @else
                                    <span class="article-reading-list__sidebar-empty">{{ t('No categories found') }}</span>
                                @endif
                                <span class="article-reading-list__sidebar-empty" data-article-category-no-results hidden>{{ t('No matching categories') }}</span>
                            </div>
                        </section>
                    @endif

                    @if ($popularEnabled)
                        <section class="article-reading-list__sidebar-panel article-reading-list__popular" data-article-sidebar-position="{{ $popularPosition }}">
                            <h2>{{ t('Popular Posts') }}</h2>
                            <div class="article-reading-list__popular-list">
                                @forelse ($popularArticles as $popularArticle)
                                    @php($popularThumbnailUrl = $popularArticle->thumb_s && Storage::disk('public')->exists($popularArticle->thumb_s) ? Storage::url($popularArticle->thumb_s) : asset('assets/images/article/article-image-placeholder.svg'))
                                    <article class="article-reading-list__popular-item">
                                        @include('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $popularArticle->uri), 'class' => 'article-reading-list__popular-media', 'mediaUrl' => $popularThumbnailUrl, 'title' => $popularArticle->title])
                                        <div class="article-reading-list__popular-body">
                                            <a href="{{ route('cms.core.article.detail', $popularArticle->uri) }}">{{ $popularArticle->title }}</a>
                                            <span>{{ optional($popularArticle->created_at)->format('d M Y') }}</span>
                                        </div>
                                    </article>
                                @empty
                                    <span class="article-reading-list__sidebar-empty">{{ t('No popular posts found') }}</span>
                                @endforelse
                            </div>
                        </section>
                    @endif
                </aside>
            @endif
        </div>
    </div>
</section>
