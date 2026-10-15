<?php
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
?>

<section class="article-page article-page--reading-list">
    <div class="article-shell" <?php echo $__env->make('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>>
        <?php echo $__env->make('article.templates.partials.archive-header', ['templateKey' => 'minimal-reading-list', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Minimal Reading List'), 'defaultDescription' => t('Thoughtful reads on design, technology, and the web.')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="article-reading-list-layout <?php echo e($sidebarVisible ? '' : 'article-reading-list-layout--without-sidebar'); ?>">
            <div class="article-reading-list__main">
                <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
                <div data-article-vue-list-content>
                    <div class="article-reading-list__heading">
                        <h2><?php echo e(request()->filled('search') ? t('Search results') : t('All articles')); ?></h2>
                        <span><?php echo e(number_format($articles->total())); ?> <?php echo e(t('articles')); ?></span>
                    </div>
                    <div class="article-reading-list" data-article-list style="--article-reading-list-post-gap:<?php echo e($postListGap); ?>">
                        <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php ($readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 220))); ?>
                            <?php ($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg')); ?>
                            <article class="article-reading-list__item">
                                <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-reading-list__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <div class="article-reading-list__body">
                                    <div class="article-meta"><?php if(data_get($templateOptions, 'display.category', true)): ?><span><?php echo e($article->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?> <?php if(data_get($templateOptions, 'display.date', true)): ?><span><?php echo e(optional($article->created_at)->format('d M Y')); ?></span><?php endif; ?> <?php if(data_get($templateOptions, 'display.reading_time', true)): ?><span><?php echo e($readingMinutes); ?> <?php echo e(t('min read')); ?></span><?php endif; ?></div>
                                    <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php if(data_get($templateOptions, 'display.excerpt', true)): ?><p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180)); ?></p><?php endif; ?>
                                    <?php if(data_get($templateOptions, 'display.read_more', true)): ?><a class="article-text-link article-reading-list__read-link" href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e(t('Read more')); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="article-empty"><?php echo e(t('No articles found')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="article-vue-control-slot" data-article-vue-control-slot></div>
                <?php echo $__env->make('article.templates.partials.pagination', ['articles' => $articles], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <?php if($sidebarVisible): ?>
                <aside class="article-reading-list__sidebar" aria-label="<?php echo e(t('Article discovery')); ?>">
                    <?php if($categoriesPanelVisible): ?>
                        <section class="article-reading-list__sidebar-panel article-reading-list__categories" data-article-sidebar-position="<?php echo e($categoriesPosition); ?>">
                            <h2><?php echo e(t('Categories')); ?></h2>
                            <form class="article-reading-list__category-search" action="<?php echo e(route('cms.core.article')); ?>" method="get" role="search" data-article-category-search-form>
                                <label class="visually-hidden" for="article-minimal-reading-list-category-search"><?php echo e(t('Search categories')); ?></label>
                                <input id="article-minimal-reading-list-category-search" type="search" placeholder="<?php echo e(t('Search categories')); ?>" autocomplete="off" data-article-category-search>
                            </form>
                            <div class="article-reading-list__category-list">
                                <?php if($categories->isNotEmpty()): ?>
                                    <?php ($allCategoryQuery = request()->except(['page', 'category'])); ?>
                                    <a class="article-reading-list__category-link <?php echo e($activeCategory === '' ? 'is-active' : ''); ?>" href="<?php echo e(route('cms.core.article', $allCategoryQuery)); ?>" data-article-category-link data-category-id="" data-category-label="<?php echo e(\Illuminate\Support\Str::lower(t('All categories'))); ?>" <?php if($activeCategory === ''): ?> aria-current="page" <?php endif; ?>><?php echo e(t('All categories')); ?></a>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php ($categoryQuery = array_merge(request()->except(['page', 'category']), ['category' => $category->id])); ?>
                                        <a class="article-reading-list__category-link <?php echo e($activeCategory === (string) $category->id ? 'is-active' : ''); ?>" href="<?php echo e(route('cms.core.article', $categoryQuery)); ?>" data-article-category-link data-category-id="<?php echo e($category->id); ?>" data-category-label="<?php echo e(\Illuminate\Support\Str::lower((string) $category->name)); ?>" <?php if($activeCategory === (string) $category->id): ?> aria-current="page" <?php endif; ?>><?php echo e($category->name); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="article-reading-list__sidebar-empty"><?php echo e(t('No categories found')); ?></span>
                                <?php endif; ?>
                                <span class="article-reading-list__sidebar-empty" data-article-category-no-results hidden><?php echo e(t('No matching categories')); ?></span>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php if($popularEnabled): ?>
                        <section class="article-reading-list__sidebar-panel article-reading-list__popular" data-article-sidebar-position="<?php echo e($popularPosition); ?>">
                            <h2><?php echo e(t('Popular Posts')); ?></h2>
                            <div class="article-reading-list__popular-list">
                                <?php $__empty_1 = true; $__currentLoopData = $popularArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $popularArticle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php ($popularThumbnailUrl = $popularArticle->thumb_s && Storage::disk('public')->exists($popularArticle->thumb_s) ? Storage::url($popularArticle->thumb_s) : asset('assets/images/article/article-image-placeholder.svg')); ?>
                                    <article class="article-reading-list__popular-item">
                                        <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $popularArticle->uri), 'class' => 'article-reading-list__popular-media', 'mediaUrl' => $popularThumbnailUrl, 'title' => $popularArticle->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <div class="article-reading-list__popular-body">
                                            <a href="<?php echo e(route('cms.core.article.detail', $popularArticle->uri)); ?>"><?php echo e($popularArticle->title); ?></a>
                                            <span><?php echo e(optional($popularArticle->created_at)->format('d M Y')); ?></span>
                                        </div>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="article-reading-list__sidebar-empty"><?php echo e(t('No popular posts found')); ?></span>
                                <?php endif; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/archive/minimal-reading-list.blade.php ENDPATH**/ ?>