<?php
    $journalOptions = $templateOptions['editorial_journal'] ?? [];
    $dividerEnabled = data_get($journalOptions, 'lead_grid.divider.enabled', true) !== false;
    $spacingKey = $dividerEnabled ? 'with_divider' : 'without_divider';
    $leadGridSpacing = data_get($journalOptions, 'lead_grid.spacing.'.$spacingKey, '2rem');
    $edgeToEdge = data_get($journalOptions, 'thumbnail.edge_to_edge', false) === true;
    $cardBorderEnabled = data_get($journalOptions, 'card.border.enabled', true) !== false;
    $cardBackgroundType = data_get($journalOptions, 'card.background.type', 'color');
    $cardBackgroundImage = data_get($journalOptions, 'card.background.image', '');
    $cardHeightMode = data_get($journalOptions, 'card.height.mode', 'auto');
    $readMoreEnabled = data_get($journalOptions, 'read_more.enabled', false) === true;
    $readMorePosition = data_get($journalOptions, 'read_more.position', 'left');
    $readMoreIcon = data_get($journalOptions, 'read_more.icon', 'fas fa-arrow-right');
    $grid = $templateOptions['grid'] ?? ['desktop' => 3, 'tablet' => 2, 'mobile' => 1];
    $gridStyle = '--article-template-grid-desktop:'.$grid['desktop'].';--article-template-grid-tablet:'.$grid['tablet'].';--article-template-grid-mobile:'.$grid['mobile'].';';
    $gridStyle .= '--article-editorial-lead-grid-spacing:'.$leadGridSpacing.';';
    $gridStyle .= '--article-editorial-card-height-desktop:'.data_get($journalOptions, 'card.height.desktop', '22rem').';';
    $gridStyle .= '--article-editorial-card-height-tablet:'.data_get($journalOptions, 'card.height.tablet', '22rem').';';
    $gridStyle .= '--article-editorial-card-height-mobile:'.data_get($journalOptions, 'card.height.mobile', '22rem').';';
    $cardStyle = '--article-editorial-card-border-style:'.data_get($journalOptions, 'card.border.type', 'solid').';';
    $cardStyle .= '--article-editorial-card-border-width:'.data_get($journalOptions, 'card.border.width', '1px').';';
    $cardStyle .= '--article-editorial-card-border-color:'.data_get($journalOptions, 'card.border.color', '#e6e9ef').';';
    $cardStyle .= '--article-editorial-card-border-radius:'.data_get($journalOptions, 'card.border.radius', '0.9rem').';';
    $cardStyle .= '--article-editorial-card-background-color:'.data_get($journalOptions, 'card.background.color', '#ffffff').';';
    $cardStyle .= '--article-editorial-card-background-image:none;';
    if ($cardBackgroundType === 'image' && $cardBackgroundImage !== '') {
        $safeBackgroundImage = str_replace(['\\', '"'], ['', '%22'], (string) $cardBackgroundImage);
        $cardStyle .= '--article-editorial-card-background-image:url("'.$safeBackgroundImage.'");';
    }
    $cardClass = 'article-editorial-card article-editorial-card--thumbnail-'.($edgeToEdge ? 'edge' : 'inset');
    $cardClass .= ' article-editorial-card--'.($cardBorderEnabled ? 'border' : 'no-border');
    $cardClass .= ' article-editorial-card--height-'.$cardHeightMode;
    if ($cardBackgroundType === 'image' && $cardBackgroundImage !== '') {
        $cardClass .= ' article-editorial-card--background-image';
    }
    $readMoreClass = 'article-editorial-read-more article-editorial-read-more--'.$readMorePosition;
?>

<section class="article-page article-page--editorial-journal">
    <div class="article-shell" <?php echo $__env->make('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>>
        <?php echo $__env->make('article.templates.partials.archive-header', ['templateKey' => 'editorial-journal', 'defaultEyebrow' => t('Stories'), 'defaultTitle' => t('Editorial Journal'), 'defaultDescription' => t('Featured ideas and useful perspectives.')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="article-vue-list-state-slot" data-article-vue-list-state-slot></div>
        <div data-article-vue-list-content>
            <?php ($lead = $articles->first()); ?>
            <?php if($lead): ?>
                <?php ($leadThumbnailUrl = $lead->thumb_l && Storage::disk('public')->exists($lead->thumb_l) ? Storage::url($lead->thumb_l) : asset('assets/images/article/article-image-placeholder.svg')); ?>
                <article class="article-editorial-lead article-editorial-lead--<?php echo e($dividerEnabled ? 'with' : 'without'); ?>-divider">
                    <div class="article-editorial-lead__body">
                        <span class="article-chip"><?php echo e($lead->category?->name ?? t('Uncategorized')); ?></span>
                        <?php ($article = $lead); ?>
                        <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $lead->content)), 220)); ?></p>
                        <?php if($readMoreEnabled): ?>
                            <a class="article-text-link <?php echo e($readMoreClass); ?>" href="<?php echo e(route('cms.core.article.detail', $lead->uri)); ?>"><?php echo e(t('Read more')); ?> <i class="<?php echo e($readMoreIcon); ?>" aria-hidden="true"></i></a>
                        <?php else: ?>
                            <a class="article-text-link" href="<?php echo e(route('cms.core.article.detail', $lead->uri)); ?>"><?php echo e(t('Read article')); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $lead->uri), 'class' => 'article-editorial-lead__media', 'mediaUrl' => $leadThumbnailUrl, 'title' => $lead->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </article>
            <?php endif; ?>

            <div class="article-editorial-grid article-editorial-grid--height-<?php echo e($cardHeightMode); ?>" style="<?php echo e($gridStyle); ?>">
                <?php $__currentLoopData = $articles->slice(1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($thumbnailUrl = $article->thumb_s && Storage::disk('public')->exists($article->thumb_s) ? Storage::url($article->thumb_s) : asset('assets/images/article/article-image-placeholder.svg')); ?>
                    <article class="<?php echo e($cardClass); ?>" style="<?php echo e($cardStyle); ?>">
                        <?php echo $__env->make('article.templates.partials.media-link', ['href' => route('cms.core.article.detail', $article->uri), 'class' => 'article-editorial-card__media', 'mediaUrl' => $thumbnailUrl, 'title' => $article->title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="article-editorial-card__body">
                            <span class="article-chip"><?php echo e($article->category?->name ?? t('Uncategorized')); ?></span>
                            <?php echo $__env->make('article.templates.partials.archive-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <p class="article-excerpt-clamp"><?php echo e(\Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 120)); ?></p>
                            <small><?php echo e(optional($article->created_at)->format('d M Y')); ?></small>
                            <?php if($readMoreEnabled): ?>
                                <a class="article-text-link <?php echo e($readMoreClass); ?>" href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e(t('Read more')); ?> <i class="<?php echo e($readMoreIcon); ?>" aria-hidden="true"></i></a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($articles->isEmpty()): ?><p class="article-empty"><?php echo e(t('No articles found')); ?></p><?php endif; ?>
        </div>
        <div class="article-vue-control-slot" data-article-vue-control-slot></div>
        <?php echo $__env->make('article.templates.partials.pagination', ['articles' => $articles], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</section>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/archive/editorial-journal.blade.php ENDPATH**/ ?>