<?php
    $templateOptions = app(\App\Support\Article\ArticleTemplateOptions::class)->archive('mosaic-magazine', $templateOptions ?? []);
    $mosaic = $templateOptions['mosaic_magazine'];
    $grid = $templateOptions['grid'];
    $isFiltered = trim((string) request('search', '')) !== '' || request()->filled('category') || request()->filled('tag');
    $featured = $isFiltered ? null : $articles->first();
    $supporting = $isFiltered ? collect() : $articles->slice(1, $mosaic['supporting_count']);
    $remaining = $isFiltered ? $articles->getCollection() : $articles->slice(1 + $mosaic['supporting_count']);
?>
<section class="article-page article-page--mosaic-magazine">
    <div class="article-shell" <?php echo $__env->make('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>>
        <?php echo $__env->make('article.templates.partials.archive-header', ['templateKey' => 'mosaic-magazine', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Mosaic Magazine'), 'defaultDescription' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            <?php if($isFiltered): ?>
                <div class="article-mosaic-results" role="status">
                    <span><?php echo e(number_format($articles->total())); ?> <?php echo e(t('articles')); ?><?php if(request()->filled('search')): ?> · <?php echo e(request('search')); ?><?php endif; ?> <?php if(request()->filled('category')): ?> · <?php echo e(collect($articleCategories ?? [])->firstWhere('id', request('category'))?->name ?? t('Category')); ?><?php endif; ?> <?php if(request()->filled('tag')): ?> · <?php echo e(request('tag')); ?><?php endif; ?></span>
                    <a href="<?php echo e(route('cms.core.article')); ?>" data-article-category-link><?php echo e(t('Clear filters')); ?> <i class="fas fa-times" aria-hidden="true"></i></a>
                </div>
            <?php endif; ?>

            <?php if($featured): ?>
                <?php
                    $featuredThumbnailUrl = $featured->thumb_l && Storage::disk('public')->exists($featured->thumb_l) ? Storage::url($featured->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
                    $article = $featured;
                ?>
                <article class="article-mosaic-feature" data-mosaic-region="hero">
                    <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $featured->uri), 'class' => 'article-mosaic-feature__media', 'mediaUrl' => $featuredThumbnailUrl, 'title' => $featured->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <div class="article-mosaic-feature__body"><?php if(data_get($templateOptions, 'display.category', true)): ?><span class="article-chip"><?php echo e($featured->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?>
<?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php if(data_get($templateOptions, 'display.excerpt', true)): ?><p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $featured->content)), 180)); ?></p><?php endif; ?>
<?php if(data_get($templateOptions, 'display.date', true)): ?><small><?php echo e(optional($featured->created_at)->format('d M Y')); ?></small><?php endif; ?></div>
                </article>
            <?php endif; ?>

            <?php $__currentLoopData = ['supporting' => $supporting, 'regular' => $remaining]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region => $posts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($posts->isEmpty()) continue; ?>
                <?php
                    $columns = $region === 'supporting' && !$mosaic['match_grid'] ? $mosaic['columns'] : $grid;
                    $columnStyle = '';
                    foreach ($columns as $device => $count) {
                        $count = $region === 'supporting' && $posts->count() === 1 ? 1 : $count;
                        $tracks = $region === 'supporting' && $count === 2 && $mosaic['proportion'] === 'asymmetric'
                            ? 'minmax(0, 3fr) minmax(0, 2fr)' : 'repeat('.$count.', minmax(0, 1fr))';
                        $columnStyle .= '--mosaic-columns-'.$device.':'.$tracks.';';
                    }
                ?>
                <div class="article-mosaic-grid article-mosaic-grid--<?php echo e($region); ?>" data-mosaic-region="<?php echo e($region); ?>" style="<?php echo e($columnStyle); ?>">
                <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                    ?>
                    <article class="article-mosaic-card">
                        <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-mosaic-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div><?php if(data_get($templateOptions, 'display.category', true)): ?><span class="article-chip"><?php echo e($article->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?>
<?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php if(data_get($templateOptions, 'display.excerpt', true)): ?><p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 92)); ?></p><?php endif; ?>
<?php if(data_get($templateOptions, 'display.date', true)): ?><small><?php echo e(optional($article->created_at)->format('d M Y')); ?></small><?php endif; ?></div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($articles->isEmpty()): ?><p class="article-empty"><?php echo e(t('No articles found')); ?></p><?php endif; ?>
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>

        <?php echo $__env->make('article.templates.partials.pagination', ['articles' => $articles], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</section>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/archive/mosaic-magazine.blade.php ENDPATH**/ ?>