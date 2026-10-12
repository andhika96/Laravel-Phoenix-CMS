<?php
    $templateOptions = app(\App\Support\Article\ArticleTemplateOptions::class)->archive('balanced-card-grid', $templateOptions ?? []);
    $balanced = $templateOptions['balanced_grid'];
    $grid = $templateOptions['grid'];
    $balancedStyle = '--balanced-card-padding:'.$balanced['card_padding'].';--balanced-card-radius:'.$balanced['card_radius'].';--balanced-excerpt-lines:'.$balanced['excerpt_lines'].';';
    foreach (['desktop', 'tablet', 'mobile'] as $device) $balancedStyle .= '--story-gap-'.$device.':'.$balanced['gap'][$device].';';
?>
<section class="article-page article-page--balanced-grid" data-card-style="<?php echo e($balanced['card_style']); ?>" data-card-align="<?php echo e($balanced['text_align']); ?>" style="<?php echo e($balancedStyle); ?>">
    <div class="article-shell" <?php echo $__env->make('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>>
        <?php echo $__env->make('article.templates.partials.archive-header', ['templateKey' => 'balanced-card-grid', 'defaultEyebrow' => t('Explore'), 'defaultTitle' => t('Balanced Card Grid'), 'defaultDescription' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            <?php if(request()->filled('search') || request()->filled('category') || request()->filled('tag')): ?>
                <div class="article-story-results" role="status"><span><?php echo e(number_format($articles->total())); ?> <?php echo e(t('articles')); ?><?php if(request()->filled('search')): ?> · <?php echo e(request('search')); ?><?php endif; ?></span><a href="<?php echo e(route('cms.core.article')); ?>" data-article-category-link><?php echo e(t('Clear filters')); ?> <i class="fas fa-times" aria-hidden="true"></i></a></div>
            <?php endif; ?>
            <div class="article-card-grid" style="--article-template-grid-desktop:<?php echo e($grid['desktop']); ?>;--article-template-grid-tablet:<?php echo e($grid['tablet']); ?>;--article-template-grid-mobile:<?php echo e($grid['mobile']); ?>">
                <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                    ?>
                    <article class="article-card">
                        <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="article-card__body">
                            <?php if($balanced['show_category']): ?><span class="article-chip"><?php echo e($article->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?>
                            <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if($balanced['show_excerpt']): ?><p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 300)); ?></p><?php endif; ?>
                            <div class="article-balanced-footer">
                                <?php if($balanced['show_date'] && $article->created_at): ?><time datetime="<?php echo e($article->created_at->toDateString()); ?>"><?php echo e($article->created_at->format('d M Y')); ?></time><?php endif; ?>
                                <?php if($balanced['show_read_more']): ?><a class="article-text-link" href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e(t('Read article')); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: <?php echo e($article->title); ?></span></a><?php endif; ?>
                            </div>
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
</section>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/archive/balanced-card-grid.blade.php ENDPATH**/ ?>