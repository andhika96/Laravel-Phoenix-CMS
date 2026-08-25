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
    $positions = ['left', 'center', 'right'];
@endphp
<header class="article-page-heading article-template-header">
    <div class="article-template-header__copy">
        @if (data_get($copy, 'eyebrow.enabled') && data_get($copy, 'eyebrow.text'))<p class="article-eyebrow">{{ data_get($copy, 'eyebrow.text') }}</p>@endif
        @if (data_get($copy, 'title.enabled') && data_get($copy, 'title.text'))<h1>{{ data_get($copy, 'title.text') }}</h1>@endif
        @if (data_get($copy, 'description.enabled') && data_get($copy, 'description.text'))<p>{{ data_get($copy, 'description.text') }}</p>@endif
    </div>

    @if (data_get($search, 'enabled') || data_get($category, 'enabled'))
        <form class="article-template-toolbar" action="{{ route('cms.core.article') }}" method="get" data-article-filter>
            @foreach ($positions as $position)
                <div class="article-template-toolbar__zone article-template-toolbar__zone--{{ $position }}">
                    @if (data_get($search, 'enabled') && data_get($search, 'position') === $position)
                        <div class="article-template-toolbar__control article-template-toolbar__control--search">
                            <label class="visually-hidden" for="article-{{ $templateKey }}-search">{{ t('Search articles') }}</label>
                            <input id="article-{{ $templateKey }}-search" name="search" type="search" value="{{ request('search') }}" placeholder="{{ t('Search articles') }}">
                            <button type="submit" class="btn ph-btn-theme"><i class="fas fa-search" aria-hidden="true"></i><span>{{ t('Search') }}</span></button>
                        </div>
                    @endif
                    @if (data_get($category, 'enabled') && data_get($category, 'position') === $position)
                        <div class="article-template-toolbar__control article-template-toolbar__control--category">
                            <label class="visually-hidden" for="article-{{ $templateKey }}-category">{{ t('Category') }}</label>
                            <select id="article-{{ $templateKey }}-category" name="category"><option value="">{{ t('All categories') }}</option>@foreach ($categories as $item)<option value="{{ $item->id }}" @selected((string) $item->id === request('category'))>{{ $item->name }}</option>@endforeach</select>
                            <button type="submit" class="btn btn-outline-secondary">{{ t('Filter') }}</button>
                        </div>
                    @endif
                </div>
            @endforeach
        </form>
    @endif
</header>
