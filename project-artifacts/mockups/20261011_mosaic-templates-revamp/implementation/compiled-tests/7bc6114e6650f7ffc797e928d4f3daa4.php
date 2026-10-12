<?php
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
    $isEditorialJournal = $templateKey === 'editorial-journal';
    $usesCompactToolbar = in_array($templateKey, ['mosaic-magazine', 'mosaic-classic', 'balanced-card-grid'], true);
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
?>
<header class="article-page-heading article-template-header article-template-header--<?php echo e($templateKey); ?>">
    <div class="article-template-header__copy">
        <?php if(data_get($copy, 'eyebrow.enabled') && data_get($copy, 'eyebrow.text')): ?><p class="article-eyebrow"><?php echo e(data_get($copy, 'eyebrow.text')); ?></p><?php endif; ?>
        <?php if(data_get($copy, 'title.enabled') && data_get($copy, 'title.text')): ?><h1><?php echo e(data_get($copy, 'title.text')); ?></h1><?php endif; ?>
        <?php if(!data_get($copy, 'title.enabled') || !data_get($copy, 'title.text')): ?><h1 class="visually-hidden"><?php echo e(t('Articles')); ?></h1><?php endif; ?>
        <?php if(data_get($copy, 'description.enabled') && data_get($copy, 'description.text')): ?><p><?php echo e(data_get($copy, 'description.text')); ?></p><?php endif; ?>
    </div>

    <?php if(data_get($search, 'enabled') || $showCategorySelect): ?>
        <form class="article-template-toolbar" action="<?php echo e(route('cms.core.article')); ?>" method="get" data-article-filter>
            <?php if($usesCompactToolbar && request()->filled('tag')): ?><input type="hidden" name="tag" value="<?php echo e(request('tag')); ?>"><?php endif; ?>
            <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="article-template-toolbar__zone article-template-toolbar__zone--<?php echo e($position); ?>">
                    <?php if(data_get($search, 'enabled') && data_get($search, 'position') === $position): ?>
                        <div class="article-template-toolbar__control article-template-toolbar__control--search article-search-model--<?php echo e($searchType); ?>" style="<?php echo e($searchStyle); ?>">
                            <label class="visually-hidden" for="article-<?php echo e($templateKey); ?>-search"><?php echo e(t('Search articles')); ?></label>
                            <input id="article-<?php echo e($templateKey); ?>-search" name="search" type="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(t('Search articles')); ?>">
                            <button type="submit" class="btn ph-btn-theme" aria-label="<?php echo e(t('Search')); ?>"><i class="<?php echo e($searchIcon); ?>" aria-hidden="true"></i><span <?php if($usesCompactToolbar && $templateKey === 'balanced-card-grid'): ?> class="visually-hidden" <?php endif; ?>><?php echo e(t('Search')); ?></span></button>
                        </div>
                    <?php endif; ?>
                    <?php if($showCategorySelect && data_get($category, 'position') === $position): ?>
                        <div class="article-template-toolbar__control article-template-toolbar__control--category">
                            <label class="visually-hidden" for="article-<?php echo e($templateKey); ?>-category"><?php echo e(t('Category')); ?></label>
                            <select id="article-<?php echo e($templateKey); ?>-category" name="category" <?php if($isMinimalReadingList || $usesCompactToolbar || $isEditorialJournal): ?> data-article-category-select <?php endif; ?>><option value=""><?php echo e(t('All categories')); ?></option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($item->id); ?>" <?php if((string) $item->id === request('category')): echo 'selected'; endif; ?>><?php echo e($item->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                            <?php if(!$isMinimalReadingList && !$usesCompactToolbar && !$isEditorialJournal): ?><button type="submit" class="btn btn-outline-secondary"><?php echo e(t('Filter')); ?></button><?php endif; ?>
                            <?php if($usesCompactToolbar || $isEditorialJournal): ?><noscript><button type="submit" class="btn ph-btn-theme"><?php echo e(t('Filter')); ?></button></noscript><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </form>
    <?php endif; ?>
</header>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/partials/archive-header.blade.php ENDPATH**/ ?>