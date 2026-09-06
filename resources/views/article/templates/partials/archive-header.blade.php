@php
    $header = $templateOptions['header'] ?? [];
    $toolbar = $templateOptions['toolbar'] ?? [];
    $categories = $articleCategories ?? collect();
    $copy = [
        'eyebrow' => data_get($header, 'eyebrow', ['enabled' => true, 'text' => $defaultEyebrow]),
        'title' => data_get($header, 'title', ['enabled' => true, 'text' => $defaultTitle]),
        'description' => data_get($header, 'description', ['enabled' => true, 'text' => $defaultDescription]),
    ];
    $search = data_get($toolbar, 'search', ['enabled' => false, 'position' => 'left']);
    $category = data_get($toolbar, 'category', ['enabled' => false, 'position' => 'right']);
    $isMinimalReadingList = $templateKey === 'minimal-reading-list';
    $categoryMode = data_get($category, 'mode', $isMinimalReadingList ? 'button-list' : 'select');
    $categoryMode = in_array($categoryMode, ['select', 'button-list'], true) ? $categoryMode : 'select';
    $showCategorySelect = data_get($category, 'enabled') && (!$isMinimalReadingList || $categoryMode === 'select');
    $searchType = data_get($search, 'type', 'attached');
    $searchType = in_array($searchType, ['attached', 'soft', 'underline'], true) ? $searchType : 'attached';
    $searchIcon = data_get($search, 'icon', 'fas fa-search');
    $searchIcon = in_array($searchIcon, ['fas fa-search', 'fas fa-sliders-h', 'fas fa-arrow-right'], true) ? $searchIcon : 'fas fa-search';
    $searchStyle = '--article-search-radius:'.data_get($search, 'radius', '0.75rem').';--article-search-gap:'.data_get($search, 'gap', '0.75rem').';';
    foreach ([
        'input_background_color' => '--article-search-input-background',
        'input_text_color' => '--article-search-input-text',
        'input_border_color' => '--article-search-input-border',
        'button_background_color' => '--article-search-button-background',
        'button_text_color' => '--article-search-button-text',
        'button_hover_background_color' => '--article-search-button-hover-background',
        'button_hover_text_color' => '--article-search-button-hover-text',
        'button_active_background_color' => '--article-search-button-active-background',
        'button_active_text_color' => '--article-search-button-active-text',
    ] as $key => $property) {
        $color = data_get($search, $key);
        if (is_string($color) && preg_match('/^(?:#[0-9a-f]{3,8}|rgba?\([^)]*\)|hsla?\([^)]*\))$/i', $color) === 1) {
            $searchStyle .= $property.':'.$color.';';
        }
    }
    $positions = ['left', 'center', 'right'];
@endphp
<header class="article-page-heading article-template-header article-template-header--{{ $templateKey }}">
    <div class="article-template-header__copy">
        @if (data_get($copy, 'eyebrow.enabled') && data_get($copy, 'eyebrow.text'))<p class="article-eyebrow">{{ data_get($copy, 'eyebrow.text') }}</p>@endif
        @if (data_get($copy, 'title.enabled') && data_get($copy, 'title.text'))<h1>{{ data_get($copy, 'title.text') }}</h1>@endif
        @if (!data_get($copy, 'title.enabled') || !data_get($copy, 'title.text'))<h1 class="visually-hidden">{{ t('Articles') }}</h1>@endif
        @if (data_get($copy, 'description.enabled') && data_get($copy, 'description.text'))<p>{{ data_get($copy, 'description.text') }}</p>@endif
    </div>

    @if (data_get($search, 'enabled') || $showCategorySelect)
        <form class="article-template-toolbar" action="{{ route('cms.core.article') }}" method="get" data-article-filter>
            @foreach ($positions as $position)
                <div class="article-template-toolbar__zone article-template-toolbar__zone--{{ $position }}">
                    @if (data_get($search, 'enabled') && data_get($search, 'position') === $position)
                        <div class="article-template-toolbar__control article-template-toolbar__control--search article-search-model--{{ $searchType }}" style="{{ $searchStyle }}">
                            <label class="visually-hidden" for="article-{{ $templateKey }}-search">{{ t('Search articles') }}</label>
                            <input id="article-{{ $templateKey }}-search" name="search" type="search" value="{{ request('search') }}" placeholder="{{ t('Search articles') }}">
                            <button type="submit" class="btn ph-btn-theme"><i class="{{ $searchIcon }}" aria-hidden="true"></i><span>{{ t('Search') }}</span></button>
                        </div>
                    @endif
                    @if ($showCategorySelect && data_get($category, 'position') === $position)
                        <div class="article-template-toolbar__control article-template-toolbar__control--category">
                            <label class="visually-hidden" for="article-{{ $templateKey }}-category">{{ t('Category') }}</label>
                            <select id="article-{{ $templateKey }}-category" name="category" @if ($isMinimalReadingList) data-article-category-select @endif><option value="">{{ t('All categories') }}</option>@foreach ($categories as $item)<option value="{{ $item->id }}" @selected((string) $item->id === request('category'))>{{ $item->name }}</option>@endforeach</select>
                            @if (!$isMinimalReadingList)<button type="submit" class="btn btn-outline-secondary">{{ t('Filter') }}</button>@endif
                        </div>
                    @endif
                </div>
            @endforeach
        </form>
    @endif
</header>
