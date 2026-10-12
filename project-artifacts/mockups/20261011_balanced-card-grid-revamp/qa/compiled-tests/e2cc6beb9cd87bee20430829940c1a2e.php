<?php if(($previousArticle ?? null) || ($nextArticle ?? null)): ?>
    <nav class="article-detail-navigation" aria-label="<?php echo e(t('Article navigation')); ?>">
        <?php if($previousArticle ?? null): ?>
            <a class="article-detail-navigation__item article-detail-navigation__item--previous" href="<?php echo e(route('cms.core.article.detail', $previousArticle->uri)); ?>">
                <img src="<?php echo e($previousArticle->thumb_s && Storage::disk('public')->exists($previousArticle->thumb_s) ? Storage::url($previousArticle->thumb_s) : asset('assets/images/article/article-image-placeholder.svg')); ?>" alt="">
                <span><small><i class="far fa-arrow-left" aria-hidden="true"></i> <?php echo e(t('Previous article')); ?></small><strong class="article-title-clamp"><?php echo e($previousArticle->title); ?></strong></span>
            </a>
        <?php else: ?>
            <span></span>
        <?php endif; ?>

        <?php if($nextArticle ?? null): ?>
            <a class="article-detail-navigation__item article-detail-navigation__item--next" href="<?php echo e(route('cms.core.article.detail', $nextArticle->uri)); ?>">
                <span><small><?php echo e(t('Next article')); ?> <i class="far fa-arrow-right" aria-hidden="true"></i></small><strong class="article-title-clamp"><?php echo e($nextArticle->title); ?></strong></span>
                <img src="<?php echo e($nextArticle->thumb_s && Storage::disk('public')->exists($nextArticle->thumb_s) ? Storage::url($nextArticle->thumb_s) : asset('assets/images/article/article-image-placeholder.svg')); ?>" alt="">
            </a>
        <?php else: ?>
            <span></span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/partials/detail-navigation.blade.php ENDPATH**/ ?>