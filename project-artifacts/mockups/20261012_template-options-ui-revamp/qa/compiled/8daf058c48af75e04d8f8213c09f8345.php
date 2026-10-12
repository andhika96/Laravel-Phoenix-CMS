<?php
    $templateOptions = app(\App\Support\Article\ArticleTemplateOptions::class)->archive('mosaic-classic', $templateOptions ?? []);
    $classic = $templateOptions['mosaic_classic'];
    $grid = $templateOptions['grid'];
    $filtered = trim((string) request('search', '')) !== '' || request()->filled('category') || request()->filled('tag');
    $lead = $filtered ? null : $articles->first();
    $sidebarArticles = $filtered ? collect() : $articles->slice(1, $classic['side_count']);
    $highlightArticles = $filtered ? collect() : $articles->slice(1 + $classic['side_count'], $classic['highlight_count']);
    $gridArticles = $filtered ? $articles->getCollection() : $articles->slice(1 + $classic['side_count'] + $classic['highlight_count']);
    $classicStyle = '';
    foreach (['desktop', 'tablet', 'mobile'] as $device) {
        $classicStyle .= '--classic-lead-height-'.$device.':'.$classic['lead_height'][$device].';';
        $classicStyle .= '--classic-side-height-'.$device.':'.$classic['side_height'][$device].';';
        $classicStyle .= '--story-gap-'.$device.':'.$classic['gap'][$device].';';
        $classicStyle .= '--classic-highlight-height-'.$device.':'.$classic['highlight_height'][$device].';';
        $classicStyle .= '--classic-highlight-columns-'.$device.':'.$classic['highlight_columns'][$device].';';
    }
?>
<section class="article-page article-page--mosaic-classic" data-classic-design="letsread" data-classic-divider="<?php echo e($classic['divider'] ? 'true' : 'false'); ?>" style="<?php echo e($classicStyle); ?>">
    <div class="article-shell" <?php echo $__env->make('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>>
        <?php echo $__env->make('article.templates.partials.archive-header', ['templateKey' => 'mosaic-classic', 'defaultEyebrow' => t('Journal'), 'defaultTitle' => t('Mosaic Classic'), 'defaultDescription' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            <?php if($filtered): ?>
                <div class="article-story-results" role="status"><span><?php echo e(number_format($articles->total())); ?> <?php echo e(t('articles')); ?><?php if(request()->filled('search')): ?> · <?php echo e(request('search')); ?><?php endif; ?></span><a href="<?php echo e(route('cms.core.article')); ?>" data-article-category-link><?php echo e(t('Clear filters')); ?> <i class="fas fa-times" aria-hidden="true"></i></a></div>
            <?php endif; ?>

            <?php if($lead): ?>
                <?php
                    $leadThumbnailUrl = $lead->thumb_l && Storage::disk('public')->exists($lead->thumb_l) ? Storage::url($lead->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
                    $article = $lead;
                ?>
                <div class="article-classic-layout" data-classic-side="<?php echo e($classic['side_position']); ?>" data-classic-ratio="<?php echo e($classic['lead_ratio']); ?>" data-classic-has-side="<?php echo e($sidebarArticles->isNotEmpty() ? 'true' : 'false'); ?>">
                    <article class="article-classic-lead" data-classic-region="lead">
                        <?php if($classic['lead_layout'] === 'image-first'): ?>
                        <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $lead->uri), 'class' => 'article-classic-lead__media', 'mediaUrl' => $leadThumbnailUrl, 'title' => $lead->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                        <div class="article-classic-lead__body">
                            <?php if($classic['show_category']): ?><span class="article-chip"><?php echo e($lead->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?>
                            <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if($classic['show_excerpt']): ?><p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $lead->content)), 220)); ?></p><?php endif; ?>
                            <?php echo $__env->make('article.templates.partials.classic-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if($classic['show_read_more']): ?><a class="article-text-link" href="<?php echo e(route('cms.core.article.detail', $lead->uri)); ?>"><?php echo e(t('Read article')); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: <?php echo e($lead->title); ?></span></a><?php endif; ?>
                        </div>
                        <?php if($classic['lead_layout'] === 'headline-first'): ?>
                            <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $lead->uri), 'class' => 'article-classic-lead__media', 'mediaUrl' => $leadThumbnailUrl, 'title' => $lead->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                    </article>

                    <?php if($sidebarArticles->isNotEmpty()): ?>
                    <aside class="article-classic-sidebar" data-classic-region="side" data-classic-side-layout="<?php echo e($classic['side_layout']); ?>" data-classic-image-position="<?php echo e($classic['side_image_position']); ?>" aria-label="<?php echo e(t('More stories')); ?>">
                        <?php $__currentLoopData = $sidebarArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                            ?>
                            <article class="article-classic-sidebar__item">
                                <?php if($classic['side_image_position'] === 'left' || $classic['side_layout'] === 'stacked'): ?>
                                <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-classic-sidebar__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endif; ?>
                                <div>
                                    <?php if($classic['side_numbers']): ?><span class="article-classic-number" aria-hidden="true"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span><?php endif; ?>
                                    <?php if($classic['show_category']): ?><span class="article-chip"><?php echo e($article->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?>
                                    <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php if($classic['show_excerpt']): ?><p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 100)); ?></p><?php endif; ?>
                                    <?php echo $__env->make('article.templates.partials.classic-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php if($classic['show_read_more']): ?><a class="article-text-link" href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e(t('Read article')); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: <?php echo e($article->title); ?></span></a><?php endif; ?>
                                </div>
                                <?php if($classic['side_image_position'] === 'right' && $classic['side_layout'] !== 'stacked'): ?>
                                    <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-classic-sidebar__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </aside>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($highlightArticles->isNotEmpty()): ?>
                <div class="article-classic-highlights" data-classic-region="highlights">
                    <?php $__currentLoopData = $highlightArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $highlightThumbnail = $article->thumb_l && Storage::disk('public')->exists($article->thumb_l) ? Storage::url($article->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
                        ?>
                        <article class="article-classic-highlight">
                            <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-classic-highlight__media', 'mediaUrl' => $highlightThumbnail, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="article-classic-highlight__body">
                                <?php if($classic['show_category']): ?><span class="article-chip"><?php echo e($article->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?>
                                <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php echo $__env->make('article.templates.partials.classic-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php if($classic['show_read_more']): ?><a class="article-text-link" href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e(t('Read article')); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: <?php echo e($article->title); ?></span></a><?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <?php if($gridArticles->isNotEmpty()): ?>
            <?php if(!$filtered && $classic['latest_title'] !== ''): ?><h2 class="article-classic-section-title"><?php echo e($classic['latest_title']); ?></h2><?php endif; ?>
            <div class="article-card-grid article-classic-grid" data-classic-region="grid" style="--article-template-grid-desktop:<?php echo e($grid['desktop']); ?>;--article-template-grid-tablet:<?php echo e($grid['tablet']); ?>;--article-template-grid-mobile:<?php echo e($grid['mobile']); ?>">
                <?php $__currentLoopData = $gridArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg');
                    ?>
                    <article class="article-card">
                        <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="article-card__body">
                            <?php if($classic['show_category']): ?><span class="article-chip"><?php echo e($article->category?->name ?? t('Uncategorized')); ?></span><?php endif; ?>
                            <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if($classic['show_excerpt']): ?><p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 150)); ?></p><?php endif; ?>
                            <?php echo $__env->make('article.templates.partials.classic-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if($classic['show_read_more']): ?><a class="article-text-link" href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e(t('Read article')); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">: <?php echo e($article->title); ?></span></a><?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php if($articles->isEmpty()): ?><p class="article-empty"><?php echo e(t('No articles found')); ?></p><?php endif; ?>
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>
        <?php echo $__env->make('article.templates.partials.pagination', ['articles' => $articles], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</section>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/archive/mosaic-classic.blade.php ENDPATH**/ ?>