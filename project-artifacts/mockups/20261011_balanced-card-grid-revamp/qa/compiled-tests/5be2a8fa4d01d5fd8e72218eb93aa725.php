<?php ($coverUrl = $article->thumb_l && Storage::disk('public')->exists($article->thumb_l) ? Storage::url($article->thumb_l) : asset('assets/images/article/article-image-placeholder.svg')); ?>
<article class="article-detail article-detail--editorial-feature">
    <header class="article-feature-hero article-media-frame">
        <img src="<?php echo e($coverUrl); ?>" alt="<?php echo e($article->title); ?>">
        <div class="article-feature-hero__overlay article-detail__header">
            <?php echo $__env->make('article.templates.partials.detail-header-copy', ['defaultEyebrow' => $article->category?->name ?? t('Uncategorized'), 'defaultDescription' => \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180), 'metaText' => optional($article->created_at)->format('d M Y').' · '.t('By').' '.($article->author?->fullname ?: $article->author?->username)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </header>
    <div class="article-detail__shell" <?php echo $__env->make('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>>
        <a href="<?php echo e(route('cms.core.article')); ?>" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> <?php echo e(t('Back to articles')); ?></a>
        <div class="article-rich-content article-rich-content--feature"><?php echo $article->content; ?></div>
        <?php echo $__env->make('article.templates.partials.detail-navigation', ['previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</article>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/detail/editorial-feature.blade.php ENDPATH**/ ?>